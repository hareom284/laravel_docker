<?php

namespace Src\Company\Document\Application\Repositories\Eloquent;

use stdClass;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Src\Company\Document\Domain\Mail\SendEmailCopy;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Src\Company\UserManagement\Domain\Resources\UserResource;
use Src\Company\Document\Application\DTO\RenovationDocumentData;
use Src\Company\Document\Domain\Resources\PendingRenoDocResource;
use Src\Company\Document\Domain\Resources\RenovationItemsResource;
use Src\Company\Document\Domain\Model\Entities\RenovationDocuments;
use Src\Company\Document\Application\Mappers\RenovationDocumentsMapper;
use Src\Company\Document\Domain\Repositories\RenovationDocumentInterface;
use Src\Company\System\Infrastructure\EloquentModels\CompanyEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\ProjectEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\AOWIndexEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\SectionsEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\ItemsIndexEloquentModel;
use Src\Company\System\Infrastructure\EloquentModels\GeneralSettingEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\SectionsIndexEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\RenovationItemsEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\DocumentStandardEloquentModel;
use Src\Company\Document\Application\UseCases\Queries\FindAllRenovationDocumentsQuery;
use Src\Company\Document\Infrastructure\EloquentModels\RenovationSettingEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\SectionAreaOfWorkEloquentModel;
// use Illuminate\Support\Facades\Log;
use Src\Company\CustomerManagement\Infrastructure\EloquentModels\CustomerEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\RenovationSectionsEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\RenovationDocumentsEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\RenovationAreaOfWorkEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\QuotationTemplateItemsEloquentModel;

class RenovationDocumentRepository implements RenovationDocumentInterface
{
    public function getRenovationDocumentsIndex($projectId, $type)
    {
        $final_result = [];

        $isCustomer = auth()->check() && auth()->user()?->roles?->pluck('name')->contains('Customer') ?? false;


        $data = RenovationDocumentsEloquentModel::with('updatedUsers','approvers')->where([
            ['project_id', $projectId],
            ['type', $type]
        ])->when($isCustomer, function ($query) {
            return $query->where('customer_signature', '<>', '');
        })->get();




        foreach ($data as $item) {

            if($type == 'CANCELLATION'){
                $countForItems = RenovationItemsEloquentModel::where('renovation_document_id', $item->id)->where('is_excluded',0)->count(); // Because of Unchecked Parent Items
            } else {
                $countForItems = RenovationItemsEloquentModel::where('renovation_document_id', $item->id)->count();
            }

            $obj = new stdClass;
            $obj->id = $item->id;
            $obj->version_number = $item->version_number;
            $obj->status = $item->status;
            $obj->signed_date = $item->signed_date;
            $obj->total_items = $countForItems;
            $obj->created_at = $item->created_at;
            $obj->updated_at = $item->updated_at;
            $obj->salesperson_signature = $item->salesperson_signature;
            $obj->updated_user = $item->updatedUsers ? $item->updatedUsers->first_name . ' ' . $item->updatedUsers->last_name : '';
            $obj->uploaded_pdf_file = $item->getFirstMediaUrl('document_file');
            $obj->approvers = $item->approvers;

            array_push($final_result, $obj);
        }

        return $final_result;
    }

    public function getRenovationDocuments($renovation_document_id, $type)
    {
        // Find renovation documents detail based on id
        $data = RenovationDocumentsEloquentModel::with('renovation_items', 'projects.customer.customers', 'salesperson.user', 'salesperson.rank', 'customer_signatures.customer.customers')->find($renovation_document_id);
        $currentTypeCheck = $type;


        //handle by wai yan check items quantity for variation order
        if ($type == 'VARIATIONORDER') {
            $projectId = $data->project_id;
            $sign_quotation_id = RenovationDocumentsEloquentModel::where('project_id', $projectId)
                ->where('type', 'QUOTATION')
                ->whereNotNull('signed_date')
                ->pluck('id')
                ->first();
            // $lastSignedVO = RenovationDocumentsEloquentModel::where('project_id', $projectId)
            // ->with('renovation_items')
            // ->where('type', 'VARIATIONORDER')
            // ->where('id','!=',$renovation_document_id)
            // ->whereNotNull('signed_date')
            // ->pluck('id')
            // ->last();
            $lastSignedVO = RenovationDocumentsEloquentModel::where('project_id', $projectId)
            ->with('renovation_items')
            ->where('type', 'VARIATIONORDER')
            ->where('id', '!=', $renovation_document_id)
            ->whereNotNull('signed_date')
            ->orderBy('signed_date', 'asc')
            ->get()
            ->filter(function ($vo) use ($renovation_document_id) {
                return $vo->id < $renovation_document_id;
            })
            ->pluck('id')
            ->last();
            $original_signed_quotaiton_lists = (new FindAllRenovationDocumentsQuery($sign_quotation_id, 'QUOTATION'))->handle();
            $original_renovation_items = $original_signed_quotaiton_lists->renovation_items;
            $items = $data->renovation_items;

            // replace the original quantity with signed vo quantity
            if ($lastSignedVO) {
                $lastSignedVODetails = RenovationDocumentsEloquentModel::with('renovation_items')
                ->find($lastSignedVO);
               $lastSignVOItems = $lastSignedVODetails->renovation_items;
               foreach ($original_renovation_items as $original_renovation_item) {
               foreach($lastSignVOItems as $lastSignVOItem){
                    if($lastSignVOItem->prev_item_id == $original_renovation_item->id){
                       $original_renovation_item->quantity = $lastSignVOItem->quantity;
                       $original_renovation_item->length = $lastSignVOItem->length;
                       $original_renovation_item->breadth = $lastSignVOItem->breadth;
                       $original_renovation_item->height = $lastSignVOItem->height;
                    }
                }
               }

            }

            foreach ($items as $item) {
                $item->current_quantity = $item->quantity;
                foreach ($original_renovation_items as $original_renovation_item) {

                    if ($item->prev_item_id == $original_renovation_item->id) {
                        $item->quantity = $item->quantity - $original_renovation_item->quantity;
                        $item->length = $item->length == null || $item->length == 1 ?
                                        $item->length :
                                        $item->length -
                                        ($original_renovation_item->length == null || $original_renovation_item->length == 1 ?
                                        0 : $original_renovation_item->length);
                        $item->breadth = $item->breadth == null || $item->breadth == 1 ?
                                        $item->breadth :
                                        $item->breadth -
                                        ($original_renovation_item->breadth == null || $original_renovation_item->breadth == 1 ?
                                        0 : $original_renovation_item->breadth);
                        $item->height = $item->height == null || $item->height == 1 ?
                                        $item->height :
                                        $item->height -
                                        ($original_renovation_item->height == null || $original_renovation_item->height == 1 ?
                                        0 : $original_renovation_item->height);
                    }
                }
            }
            $data->renovation_items = $items;
        } else {

            $data->renovation_items = $data->renovation_items;
        }

        //change customer image to base 64 image
        if ($type == 'QUOTATION' || $type == 'VARIATIONORDER' || $type == 'FOC' || $type == 'CANCELLATION') {
            $customer_base64Image = [];
            if (isset($data->customer_signatures) && count($data->customer_signatures) > 0) {
                foreach ($data->customer_signatures as $customer_sign) {
                    $customer_file_path = 'renovation_document/customer_signature_file/' . $customer_sign->customer_signature;

                    $customer_image = Storage::disk('public')->get($customer_file_path);


                    array_push($customer_base64Image, [
                        'customer' => $customer_sign->customer,
                        'customer_signature' => base64_encode($customer_image)
                    ]);
                }
            } else if ($data->customer_signature) {
                $customer_file_path = 'renovation_document/customer_signature_file/' . $data->customer_signature;

                $customer_image = Storage::disk('public')->get($customer_file_path);


                array_push($customer_base64Image, [
                    'customer' => $data->projects->customer,
                    'customer_signature' => base64_encode($customer_image)
                ]);
            }
        } else {

            $customer_base64Image = '';
            if ($data->customer_signature) {
                $customer_file_path = 'renovation_document/customer_signature_file/' . $data->customer_signature;

                $customer_image = Storage::disk('public')->get($customer_file_path);

                $customer_base64Image = base64_encode($customer_image);
            }
        }
        $pdf_file = "";
        if ($data->pdf_file) {
            $pdf_file =  asset('storage/pdfs/' . $data->pdf_file);
        }
        //change saleperson image to base 64 image
        $sale_file_path = 'renovation_document/salesperson_signature_file/' . $data->salesperson_signature;

        $sale_image = Storage::disk('public')->get($sale_file_path);

        $sale_base64Image = base64_encode($sale_image);

        //determine type
        $changeType = '';
        switch ($type) {
            case 'QUOTATION':
                $changeType = 'quotation';
                break;

            case 'VARIATIONORDER':
                $changeType = 'variation order';
                break;

            case 'FOC':
                $changeType = 'foc';
                break;

            case 'CANCELLATION':
                $changeType = 'cancellation';
                break;

            default:
                $changeType = 'quotation';
                break;
        }

        $isSingedOnOtherDoc = 0;

        if($type !== 'QUOTATION')
        {
            $document_types = collect(['VARIATIONORDER','FOC','CANCELLATION']);

            $filterExceptDocumentTypes = $document_types->filter( function ($filter_type) use($type){
            return $filter_type !== $type;
           });

           foreach($filterExceptDocumentTypes as $docType)
           {
             $isCount = RenovationDocumentsEloquentModel::where('project_id', $data->project_id)
                ->where('type', $docType)
                ->orderBy('created_at','desc')
                ->first('signed_date')?->signed_date !== null ? true : false;

             $isSingedOnOtherDoc +=$isCount;
           }
        }

        $version = '';
        switch ($currentTypeCheck) {
            case 'QUOTATION':
                $version = '/QO';
                break;

            case 'VARIATIONORDER':
                $version = '/VO';
                break;

            case 'FOC':
                $version = '/FOC';
                break;

            case 'CANCELLATION':
                $version = '/CN';
                break;

            default:
                # code...
                break;
        }

        if ($type != 'QUOTATION') {
            $version_number = RenovationDocumentsEloquentModel::where('project_id', $data->project_id)
                ->where('type', $type)
                ->get();

            foreach ($version_number as $key => $value) {

                if ($renovation_document_id == $value->id) {
                    $version .= (string)$key + 1;
                }
            }
        } else {

            $version .= $data->version_number;
        }


        //get header and footer from document standard table based on company id
        $document_standard = DocumentStandardEloquentModel::where('name', $changeType)->orderBy('id', 'desc')->where('company_id', $data->projects->company_id)->first();

        //calculate total amount for each normal items
        // $totalAmount = 0;
        // foreach ($data->renovation_items as $item) {

        //     $totalAmount = $totalAmount + ($item->quantity * $item->price);
        // }

        //Just take total amount from reno document total amount
        $totalAmount = RenovationDocumentsEloquentModel::where('id', $renovation_document_id)->value('total_amount');


        //check if this document is already sign or not
        $signStatus = false;
        $forSigningDone = RenovationDocumentsEloquentModel::where([
            ['project_id', $data->project_id],
            ['type', $type]
        ])->get(['signed_date']);
        foreach ($forSigningDone as $value) {
            if ($value->signed_date) {
                $signStatus = true;
            }
        }

        //get signed date
        $signed_date = $data->signed_date ? $data->signed_date : "";

        //get section total amount from section table based on document id
        $section_total_amount = RenovationSectionsEloquentModel::where('document_id', $renovation_document_id)->get(['id', 'total_price', 'description as section_description','section_id']);

        // logger("section_total_amount",[$section_total_amount,$renovation_document_id]);
        $items_for_sign = $this->buildNestedItems($data->renovation_items);

        // checking whether reno-doc approve is require or not
        $isAllowApproveSetting = GeneralSettingEloquentModel::where('setting','reno_approve_setting')->first()->value ?? null;
        if(($isAllowApproveSetting == 'false' || $isAllowApproveSetting == false))
        {
            $documentStatus = 'approved';
        } else {
            $documentStatus = $data->status;
        }

        //generate final result
        $final_result = new stdClass();
        $final_result->id = $data->id;
        $final_result->is_singed_on_other_doc_check = $isSingedOnOtherDoc >= 1 ? true : false;
        // $final_result->version_num = $data->version_number;
        $final_result->ver = $data->version_number;
        $final_result->agreement_no = $data->agreement_no;
        $final_result->version_num = $version;
        $final_result->additional_notes = $data->additional_notes ? $data->additional_notes : '';
        $final_result->project_id = $data->project_id;
        $final_result->salesperson_signature = $sale_base64Image;
        $final_result->customer_signature = $customer_base64Image;
        $final_result->special_discount_percentage = $data->special_discount_percentage;
        $final_result->totalAllAmount = $totalAmount;
        $final_result->header_text = $document_standard->header_text;
        $final_result->footer_text = $document_standard->footer_text;
        $final_result->payment_terms_text = $document_standard->payment_terms;
        $final_result->disclaimer = $document_standard->disclaimer;
        $final_result->terms = $document_standard->terms_and_conditions;
        $final_result->signed_date = $signed_date;
        $final_result->created_at = $data->created_at ? $data->created_at : '';
        $final_result->already_sign = $signStatus;
        $final_result->status = $documentStatus;
        $final_result->section_total_amount = $section_total_amount;
        $final_result->items = $items_for_sign;
        $final_result->pdf_file = $pdf_file;
        $final_result->renovation_items = $data->renovation_items;  //get items and use resource
        $final_result->signed_saleperson = $data->salesperson->user->first_name . ' ' . $data->salesperson->user->last_name; //combine saleperson name
        $final_result->signed_sale_email = $data->salesperson->user->email;
        $final_result->signed_sale_ph = $data->salesperson->user->contact_no;
        $final_result->rank = $data?->salesperson?->rank?->rank_name ?? '';
        $final_result->saleperson_id = $data->salesperson->id;
        $final_result->customer_ids = $data->customer_signatures;
        $final_result->ismerged = $data->ismerged ?? false;
        $final_result->payment_terms = $data->payment_terms ?? null;
        $final_result->salepersonRegistry = $data->salesperson->registry_no;
        $final_result->remark = $data->remark;

        return $final_result;
    }

    public function getRenovationItemWithSections($projectId)
    {
        $renovationDocument = RenovationItemsEloquentModel::where('project_id', $projectId)
            ->whereHas('renovation_documents', function ($query) {
                $query->where('type', 'QUOTATION')
                    ->whereNotNull('signed_date');  //one project can have only one "signed" quotation document
            })
            ->with(['renovation_documents' => function ($query) {
                $query->where('type', 'QUOTATION')
                    ->whereNotNull('signed_date');
            }])
            ->first();

        $signedDate = $renovationDocument->renovation_documents->first()->signed_date;

        $signDateCargon = Carbon::parse($signedDate);

        $tomorrow = $signDateCargon->addDay(); // get tomorrow date

        $tomorrowDate = $tomorrow->format('Y-m-d');  //start date

        $tomorrowAsCarbon = Carbon::parse($tomorrowDate);

        $oneWeekLater = $tomorrowAsCarbon->copy()->addWeek(); // Get the end date (one week later)

        $oneWeekLaterDate = $oneWeekLater->format('Y-m-d'); // end date

        $startDate = $tomorrowDate;

        $endDate = $oneWeekLaterDate;

        $renovationItems = DB::table('renovation_items')
            ->where('project_id', $projectId)
            ->join('renovation_item_sections', 'renovation_items.renovation_item_section_id', '=', 'renovation_item_sections.id')
            ->join('sections', 'renovation_item_sections.section_id', '=', 'sections.id')
            ->select(
                'renovation_items.id as item_id',
                'renovation_items.name as item_name',
                'sections.id as section_id',
                'sections.name as section_name',
                DB::raw("'$startDate' as start_date"),
                DB::raw("'$endDate' as end_date")
            )
            ->get()
            ->groupBy('section_name');

        return $renovationItems;
    }

    public function getConfirmAmtsByProjectId($projectId)
    {
        $docs = RenovationDocumentsEloquentModel::where('project_id', $projectId)
            ->whereNotNull('signed_date')
            ->get();

        if ($docs->isNotEmpty()) {

            $groupedDocs = $docs->groupBy('type');

            $modifiedDocs = $groupedDocs->map(function ($documents, $type) {
                return $documents->map(function ($document, $key) use ($type) {
                    // TODO: may need update for agreement_no
                    $name = $key > 0 ? "$type $key" : $type;

                    $version = '';
                    switch ($type) {
                        case 'QUOTATION':
                            $version = $document->projects->agreement_no;
                            break;

                        case 'VARIATIONORDER':
                            $version = $document->projects->agreement_no.'/VO';
                            break;

                        case 'FOC':
                            $version = $document->projects->agreement_no.'/FOC';
                            break;

                        case 'CANCELLATION':
                            $version = $document->projects->agreement_no.'/CN';
                            break;

                        default:
                            # code...
                            break;
                    }

                    if($type != 'QUOTATION')
                    {
                        $version_number = RenovationDocumentsEloquentModel::where('project_id', $document->projects->id)
                                                                            ->where('type', $document->type)
                                                                            ->get();

                        foreach ($version_number as $key => $value) {

                            if($document->id == $value->id) {
                                $version .= (string)$key + 1;
                            }

                        }
                    }

                    return [
                        'name' => $name,
                        'version' => $version,
                        'signed_date' => $document->signed_date,
                        'total_amount' => number_format($document->total_amount, 2, '.', ',')
                    ];
                });
            })->flatten(1);

            return $modifiedDocs->values();
        }

        return false;
    }

    public function store(RenovationDocuments $renovationDocuments, array $data): RenovationDocumentData
    {
        if(isset($renovationDocuments->id))
        {
            $isSignedAlready = RenovationDocumentsEloquentModel::find($renovationDocuments->id,['salesperson_signature'])->salesperson_signature  ?? null;

            if(empty($renovationDocuments->salesperson_signature) && !isset($isSignedAlready))
                deleteOldRecordData($renovationDocuments->id);
        }

        DB::beginTransaction();
        //save document
        $documentEloquent = RenovationDocumentsMapper::toEloquent($renovationDocuments);

        logger('message',[$documentEloquent]);

        $documentEloquent->save();
        $documentEloquent->approvers()->detach();


        //Array to store index of sections
        $sectionSequence = [];
        $renoSectionIndex = 1;
        foreach ($data as $section) {
            $section->document_id = $documentEloquent->id;

            // Validate section and get section ID
            $sectionId = $this->validateSection($section);

            //push section id into section index array
            $sectionSequence[] = $sectionId;

            $renoAOWIndex = 1;

            //Reset AOW sequence every new section
            $aowSequence = [];

            $itemCheckedCount = $this->countItemChecked($section);

            foreach ($section->area_of_works as $aow) {
                $aow->document_id = $documentEloquent->id;

                $aow->section_id = $sectionId;
                // Validate area of work and get AOW ID
                $aowId = $this->validateAreaOfWork($aow);

                //push section id into section index array
                $aowSequence[] = $aowId;

                //Check how many item is checked

                //Reset Item sequence every new AOW
                $itemSequence = [];

                foreach ($aow->items as $item) {
                    if(!empty($item->description))
                        $this->createRenovationItem($item, $documentEloquent, $sectionId, $section, $renoSectionIndex, $aowId, $aow, $renoAOWIndex, $itemCheckedCount, $itemSequence);
                }

                ItemsIndexEloquentModel::create([
                    'document_id' => $documentEloquent->id,
                    'aow_id' => $aowId,
                    'items_sequence' => json_encode($itemSequence)
                ]);
            }
            AOWIndexEloquentModel::create([
                'document_id' => $documentEloquent->id,
                'section_id' => $sectionId,
                'aow_sequence' => json_encode($aowSequence)
            ]);
        }
        SectionsIndexEloquentModel::create([
            'document_id' => $documentEloquent->id,
            'section_sequence' => json_encode($sectionSequence)
        ]);

        $renovationDocumentData = new RenovationDocumentData(
            $documentEloquent->id,
            $documentEloquent->type,
            $documentEloquent->version_number,
            $documentEloquent->disclaimer,
            $documentEloquent->special_discount_percentage,
            $documentEloquent->total_amount,
            $documentEloquent->salesperson_signature,
            $documentEloquent->signed_by_salesperson_id,
            $documentEloquent->customer_signature,
            $documentEloquent->additional_notes,
            $documentEloquent->project_id,
            $documentEloquent->document_standard_id,
            $documentEloquent->payment_terms,
            $documentEloquent->remark
        );

        DB::commit();

        return $renovationDocumentData;
    }

    //Check if section exist, if not create one and tag to doc id OR check if section exist, but different calc type
    private function validateSection($section)
    {
        //No ID means newly created section
        //Also check if calc type same incase user change calc type
        $sectionData = SectionsEloquentModel::where('id', isset($section->id) ? $section->id : null)
            ->where('calculation_type', $section->calculation_type)
            ->where('document_id', null)
            ->first();

        if (!$sectionData) {
            $sectionData = SectionsEloquentModel::create([
                'name' => $section->name,
                'calculation_type' => $section->calculation_type,
                'is_active' => 0,
                'document_id' => $section->document_id,
                'index' => 0,
                'is_misc' => 0,
                'is_page_break' => $section->is_page_break ?? false
            ]);
        }

        return $sectionData->id;
    }

    private function validateAreaOfWork($aow)
    {
        //If no ID means newly created AOW
        $aowData = SectionAreaOfWorkEloquentModel::where('id', isset($aow->id) ? $aow->id : null)
            ->where('document_id', null)
            ->first();

        if (!$aowData) {
            $aowData = SectionAreaOfWorkEloquentModel::create([
                'name' => $aow->name,
                'is_active' => 0,
                'document_id' => $aow->document_id,
                'index' => 0,
                'section_id' => $aow->section_id,
            ]);
        }

        return $aowData->id;
    }

    private function validateItem($item, $parentItem)
    {
        //No ID means newly created item
        $itemData = QuotationTemplateItemsEloquentModel::where('id', isset($item->id) ? $item->id : null)
                    ->where('document_id', null)
                    ->first();
        if (!$itemData) {
            $measurement = $item->unit_of_measurement ?? $item->measurement ?? null;

            $itemData = QuotationTemplateItemsEloquentModel::create([
                'document_id' => $item->document_id,
                'parent_id' => isset($parentItem) ? $parentItem->quotation_template_item_id : null,
                'description' => $item->description,
                'index' => 0,
                'is_active' => 0,
                'quantity' => $item->quantity,
                'unit_of_measurement' => $measurement,
                'is_fixed_measurement' => $item->is_fixed_measurement,
                'section_id' => $item->section_id,
                'area_of_work_id' => $item->area_of_work_id,
                'price_without_gst' => isset($item->price_without_gst) ?  $item->price_without_gst : 0,
                'price_with_gst' => $item->price_with_gst,
                'cost_price' =>  isset($item->cost_price) ? $item->cost_price : 0,
                'profit_margin' => isset($item->profit_margin) ? $item->profit_margin : 0,
                'sub_description' => $item->sub_description,

            ]);
        }

        return $itemData->id;
    }
    private function countItemChecked($section)
    {
        $checkedCount = 0;



        foreach ($section->area_of_works as $aow) {
            foreach ($aow->items as $item) {
                if (isset($item->isChecked) && $item->isChecked) {
                    $checkedCount++;
                }
            }
        }

        return $checkedCount;
    }

    private function validateRenovationSections($sectionId, $section, $documentEloquent, $itemCheckedCount, &$renoSectionIndex)
    {

        $renoSectionData = RenovationSectionsEloquentModel::where('section_id', $sectionId)
            ->where('name', $section->name)
            ->where('calculation_type', $section->calculation_type)
            ->where('document_id', $documentEloquent->id)
            ->first();

        if (!$renoSectionData) {
            $renoSectionData = RenovationSectionsEloquentModel::create([
                'section_id' => $sectionId,
                'name' => $section->name,
                'calculation_type' => $section->calculation_type,
                'document_id' => $documentEloquent->id,
                'total_price' => $section->total_amount,
                'total_cost_price' => $section->total_cost_amount,
                'description' => $section->description ?? "",
                'total_items_count' => $itemCheckedCount,
                'index' => $renoSectionIndex,
                'is_page_break' => $section->is_page_break ?? false
            ]);

            $renoSectionIndex++;
        }

        return ['index' => $renoSectionIndex, 'data' => $renoSectionData];
    }

    // Validate Reno Area of Work and get AOW ID
    private function validateRenoAreaOfWork($aow, $aowId, $documentId, &$renoAOWIndex)
    {
        $renoAOWData = RenovationAreaOfWorkEloquentModel::where('section_area_of_work_id', $aowId)
            ->where('name', $aow->name)
            ->where('document_id', $documentId)
            ->first();

        if (!$renoAOWData) {
            $renoAOWData = RenovationAreaOfWorkEloquentModel::create([
                'section_area_of_work_id' => $aowId,
                'name' => $aow->name,
                'document_id' => $documentId,
                'index' => $renoAOWIndex,
            ]);

            $renoAOWIndex++;
        }

        return ['index' => $renoAOWIndex, 'data' => $renoAOWData];
    }

    private function createRenovationItem($item, $documentEloquent, $sectionId, $section, $renoSectionIndex, $aowId, $aow, $renoAOWIndex, $itemCheckedCount, &$itemSequence, $parentItem = null)
    {
        $item->document_id = $documentEloquent->id;

        $item->section_id = $sectionId;
        $item->area_of_work_id = $aowId;

        $itemId = $this->validateItem($item, $parentItem);

        //Assign itemId to item id (Only use for newly created item)
        $item->id = $itemId;

        $itemSequence[] = $itemId;
        //If item checked, create a reno section, aow and item
        if (isset($item->isChecked) && $item->isChecked) {
            //Return both index and data
            $renoSection = $this->validateRenovationSections($sectionId, $section, $documentEloquent, $itemCheckedCount, $renoSectionIndex);

            //Reassign the index and data section
            $renoSectionIndex  = $renoSection['index'];
            $renoSectionData  = $renoSection['data'];

            //Return both index and data
            $renoAOW = $this->validateRenoAreaOfWork($aow, $aowId, $documentEloquent->id, $renoAOWIndex);

            //Reassign the index and data reno aow
            $renoAOWIndex  = $renoAOW['index'];
            $renoAOWData  = $renoAOW['data'];

            $cancellationId = null;
            $prevItemId = null;

            $renoItemData = $this->storeRenovationItem($documentEloquent, $item, $itemId, $renoSectionData, $renoAOWData, $cancellationId, $prevItemId, isset($parentItem) ? $parentItem->quotation_template_item_id : null);
        }

        if (!empty($item->items)) {
            foreach ($item->items as $subItem) {
                // OLD ISSUES:
                // The second condition, is to check if the newly created sub item is selected
                // Create item function should not run if a newly added sub item is not selected
                // temp_item_identifier attribute only exists, if the sub item is newly added, this is how we identify which sub item is newly added
                // if(!empty($subItem->description) && (isset($subItem->temp_item_identifier) && (isset($subItem->isChecked) && $subItem->isChecked)))
                if(!empty($subItem->description))
                    $this->createRenovationItem($subItem, $documentEloquent, $sectionId, $section, $renoSectionIndex, $aowId, $aow, $renoAOWIndex, $itemCheckedCount, $itemSequence, isset($renoItemData) ? $renoItemData : null);
            }
        }
    }

    private function storeRenovationItem($documentEloquent, $item, $itemId, $renoSectionData, $renoAOWData, $cancellationId, $prevItemId, $parentId = null)
    {
        $measurement = $item->unit_of_measurement ?? $item->measurement ?? null;
        return RenovationItemsEloquentModel::create([
            'renovation_document_id' => $documentEloquent->id,
            'parent_id' => $parentId,
            'cancellation_id' => $cancellationId,
            'prev_item_id' => $prevItemId,
            'project_id' => $documentEloquent->project_id,
            'name' => $item->description,
            'quotation_template_item_id' => isset($itemId) ? $itemId : null,
            'renovation_item_section_id' => isset($renoSectionData->id) ? $renoSectionData->id : null,
            'renovation_item_area_of_work_id' => isset($renoAOWData->id) ? $renoAOWData->id : null,
            'quantity' => isset($item->quantity) ? $item->quantity : null,
            'length' => isset($item->lengthmm) ? $item->lengthmm : null,
            'breadth' => isset($item->breadthmm) ? $item->breadthmm : null,
            'height' => isset($item->lengthmm) ? $item->lengthmm : null,
            'price' => isset($item->price_with_gst) ? $item->price_with_gst : null,
            'cost_price' => isset($item->cost_price) ? $item->cost_price : null,
            'profit_margin' => isset($item->profit_margin) ? $item->profit_margin : 0,
            'is_FOC' => isset($item->isFOC) ? $item->isFOC : 0,
            'unit_of_measurement' => $measurement,
            'is_fixed_measurement' => isset($item->is_fixed_measurement) ? $item->is_fixed_measurement : 0,
            'completed' => false,
            'active' => false,
            'sub_description' => $item->sub_description,

        ]);
    }

    public function findTemplateItemsForUpdate($document_id)
    {
        //Initialize data object
        $data = new stdClass(); // Create a new empty object
        $data->sections = []; // Initialize the sections property as an empty array

        //Get the Section index document id
        $sectionIndexArray = SectionsIndexEloquentModel::where('document_id', $document_id)
            ->pluck('section_sequence')
            ->first();

        // Convert back into array
        $sectionIndexArray = json_decode($sectionIndexArray);

        if (isset($sectionIndexArray)) {
            // Fetch all required section data in a single query
            $renovationSectionsData = RenovationSectionsEloquentModel::whereIn('section_id', $sectionIndexArray)
                ->where('document_id', $document_id)
                ->get()
                ->keyBy('section_id');

            $sectionsData = SectionsEloquentModel::whereIn('id', $sectionIndexArray)->get()->keyBy('id');

            // Fetch AOW index data for all sections in one query
            $aowIndexData = AOWIndexEloquentModel::where('document_id', $document_id)
                ->whereIn('section_id', $sectionIndexArray)
                ->pluck('aow_sequence', 'section_id');

            // Fetch all AOW data in one query
            $aowIds = [];
            foreach ($aowIndexData as $aowIndex) {
                $aowIds = array_merge($aowIds, json_decode($aowIndex, true));
            }
            $aowIds = array_unique($aowIds);

            $renovationAowData = RenovationAreaOfWorkEloquentModel::where('document_id', $document_id)
                ->whereIn('section_area_of_work_id', $aowIds)
                ->get()
                ->keyBy('section_area_of_work_id');

            $aowDataArray = SectionAreaOfWorkEloquentModel::whereIn('id', $aowIds)->get()->keyBy('id');

            // Fetch item index data for all AOWs in one query
            $itemIndexData = ItemsIndexEloquentModel::where('document_id', $document_id)
                ->whereIn('aow_id', $aowIds)
                ->pluck('items_sequence', 'aow_id');

            // Fetch all item data in one query
            $itemIds = [];
            foreach ($itemIndexData as $itemIndex) {
                $itemIds = array_merge($itemIds, json_decode($itemIndex, true));
            }
            $itemIds = array_unique($itemIds);

            $renovationItemData = RenovationItemsEloquentModel::where('renovation_document_id', $document_id)
                ->whereIn('quotation_template_item_id', $itemIds)
                // ->whereHas('quotation_items', function($query) {
                //     $query->where('is_active', 1);
                // })
                ->get()
                ->keyBy('quotation_template_item_id');

            $itemDataArray = QuotationTemplateItemsEloquentModel::whereIn('id', $itemIds)->get()->keyBy('id');
            $data->sections = [];
            foreach ($sectionIndexArray as $sectionId) {
                $section = new stdClass();

                // Check if section has data in renovation table
                $sectionData = $renovationSectionsData[$sectionId] ?? $sectionsData[$sectionId];
                $section->id = $sectionData->section_id ?? $sectionData->id;
                $section->name = $sectionData->name;
                $section->total_amount = $sectionData->total_price ?? 0;
                $section->total_cost_amount = $sectionData->total_cost_price ?? 0;
                $section->calculation_type = $sectionData->calculation_type;
                $section->description = $sectionData->description;
                $section->is_page_break = $sectionData->is_page_break ?? false;

                $aows = [];
                if (isset($aowIndexData[$sectionId])) {
                    $aowIndexArray = json_decode($aowIndexData[$sectionId], true);
                    foreach ($aowIndexArray as $aowId) {
                        $aow = new stdClass();
                        $aowData = $renovationAowData[$aowId] ?? $aowDataArray[$aowId];
                        $aow->id = $aowData->section_area_of_work_id ?? $aowData->id;
                        $aow->name = $aowData->name;

                        $items = [];
                        if (isset($itemIndexData[$aowId])) {
                            $itemIndexArray = json_decode($itemIndexData[$aowId], true);
                            foreach ($itemIndexArray as $itemId) {
                                $item = new stdClass();

                                // if(isset($renovationItemData[$itemId]) || isset($itemDataArray[$itemId]))
                                // {
                                    $itemData = $renovationItemData[$itemId] ?? $itemDataArray[$itemId];

                                    if (!isset($renovationItemData[$itemId])) {
                                        $itemData->isChecked = false;
                                        $itemData->isNew = false;
                                        $itemData->isFOC = false;
                                        $itemData->remove_check_cost_price = false;

                                    } else {
                                        $itemData->price_with_gst = $itemData->price;
                                        $itemData->isChecked = true;
                                        $itemData->isNew = false;
                                        $itemData->isFOC = $itemData->is_FOC == 0 ? false : true;
                                        $itemData->description = $itemData->name;
                                        $itemData->sub_description = $itemData->sub_description;
                                        $itemData->remove_check_cost_price = isset($itemDataArray[$itemId]) && $itemDataArray[$itemId]->document_id;
                                        // Get parent id from quotation template item
                                        $itemData->parent_id = isset($itemData->quotation_items) ?$itemData->quotation_items->parent_id : null;
                                    }
                                    $item->id = $itemData->quotation_template_item_id ?? $itemData->id;
                                    $item->quotation_template_item_id = $itemData->quotation_template_item_id;
                                    $item->parent_id = $itemData->parent_id;
                                    $item->quantity = $itemData->quantity;
                                    $item->lengthmm = $itemData->length;
                                    $item->breadthmm = $itemData->breadth;
                                    $item->heightmm = $itemData->height;
                                    $item->description = $itemData->description;
                                    $item->sub_description = $itemData->sub_description;
                                    $item->price_with_gst = $itemData->price_with_gst;
                                    $item->cost_price = $itemData->cost_price;
                                    $item->profit_margin = $itemData->profit_margin;
                                    $item->unit_of_measurement = $itemData->unit_of_measurement;
                                    $item->is_fixed_measurement = $itemData->is_fixed_measurement;
                                    $item->isChecked = $itemData->isChecked;
                                    $item->isNew = $itemData->isNew;
                                    $item->isFOC = $itemData->isFOC;
                                    $item->original_cost_price = $itemData->quotation_items ? $itemData->quotation_items->cost_price : $itemData->cost_price; // add value from template items for FE checking
                                    $item->original_profit_margin = $itemData->quotation_items ? $itemData->quotation_items->profit_margin : $itemData->profit_margin; // add value from template items for FE checking
                                    $item->remove_check_cost_price = $itemData->remove_check_cost_price ?? false;
                                    $items[] = $item;
                                }

                            // }
                        }
                        $items = $this->buildItemHierarchy($items);
                        $aow->items = $items;
                        $aows[] = $aow;
                    }
                }
                $section->area_of_works = $aows;
                $data->sections[] = $section;
            }
        }
        $versionNumber = RenovationDocumentsEloquentModel::where('id', $document_id)
            ->pluck('version_number')
            ->first();

        $specialDiscountPercentage = RenovationDocumentsEloquentModel::where('id', $document_id)
            ->pluck('special_discount_percentage')
            ->first();

        $payment_terms = RenovationDocumentsEloquentModel::where('id', $document_id)
        ->pluck('payment_terms')
        ->first();
        $hide_total = RenovationSettingEloquentModel::where([
            'renovation_document_id' => $document_id,
            'setting' => 'hide_total'
        ])->value('value') === 'true';
        $remark = RenovationDocumentsEloquentModel::where('id', $document_id)
            ->pluck('remark')
            ->first();
        $data->version_number = '/QO' . $versionNumber;
        $data->special_discount_percentage = $specialDiscountPercentage;
        $data->hide_total = $hide_total;
        $data->remark = $remark;
        $data->payment_terms = $payment_terms;

        return $data;
    }

    public function getSelectedRenovationDocuments($document_id)
    {
        $documentDetail = RenovationDocumentsEloquentModel::with('projects.customer.customers', 'customer_signatures.customer.customers')->where('id', $document_id)->first();

        $documentStandard = DocumentStandardEloquentModel::select(['header_text', 'footer_text'])->find($documentDetail->document_standard_id);

        $saleperson_base64Image = '';
        if ($documentDetail->salesperson_signature) {
            $saleperson_file_path = 'renovation_document/salesperson_signature_file/' . $documentDetail->salesperson_signature;

            $saleperson_image = Storage::disk('public')->get($saleperson_file_path);

            $saleperson_base64Image = base64_encode($saleperson_image);
        }

        $data = new stdClass;

        $data->sections = [];

        $sectionIndexArray = SectionsIndexEloquentModel::where('document_id', $document_id)
            ->pluck('section_sequence')
            ->first();

        if ($sectionIndexArray) {

            $sectionIndexArray = json_decode($sectionIndexArray);

            foreach ($sectionIndexArray as $sectionId) {

                $section = new stdClass();

                $sectionData = RenovationSectionsEloquentModel::where('section_id', $sectionId)
                    ->where('document_id', $document_id)
                    ->first();

                //If don't have, find from section table
                if (!$sectionData) {
                    $sectionData = SectionsEloquentModel::where('id', $sectionId)
                        ->first();
                }
                $section->id = isset($sectionData->section_id) ? $sectionData->section_id : $sectionData->id;
                $section->name = $sectionData->name;
                $section->total_amount = isset($sectionData->total_price) ? $sectionData->total_price : 0;
                $section->total_cost_amount = isset($sectionData->total_cost_price) ? $sectionData->total_cost_price : 0;
                $section->calculation_type = $sectionData->calculation_type;
                $section->description = $sectionData->description;
                $section->is_page_break = $sectionData->is_page_break ?? false;
                $aows = [];

                //Get the AOW index based on section and document id
                $aowIndexArray = AOWIndexEloquentModel::where('document_id', $document_id)
                    ->where('section_id', $sectionId)
                    ->pluck('aow_sequence')
                    ->first();


                if ($aowIndexArray) {
                    $aowIndexArray = json_decode($aowIndexArray);

                    foreach ($aowIndexArray as $aowId) {
                        $aow = new StdClass();
                        //Check if aow got any selected item
                        $aowData = RenovationAreaOfWorkEloquentModel::where('document_id', $document_id)
                            ->where('section_area_of_work_id', $aowId)
                            ->first();

                        if (!$aowData) {
                            $aowData = SectionAreaOfWorkEloquentModel::where('id', $aowId)
                                ->first();
                        }
                        $aow->id = isset($aowData->section_area_of_work_id) ? $aowData->section_area_of_work_id :  $aowData->id;
                        $aow->name = $aowData->name;
                        $items = [];

                        $itemIndexArray = ItemsIndexEloquentModel::where('document_id', $document_id)
                            ->where('aow_id', $aowId)
                            ->pluck('items_sequence')
                            ->first();


                        if ($itemIndexArray) {
                            $itemIndexArray = json_decode($itemIndexArray);

                            foreach ($itemIndexArray as $itemId) {
                                $item = new StdClass();
                                //Check if item is selected
                                $itemData = RenovationItemsEloquentModel::where('renovation_document_id', $document_id)
                                    ->where('quotation_template_item_id', $itemId)
                                    ->first();
                                //If no item found, means not checked
                                if (!$itemData) {
                                    $itemData = QuotationTemplateItemsEloquentModel::where('id', $itemId)
                                        ->first();
                                    $itemData->isChecked = false;
                                    $itemData->isNew = false;
                                    $itemData->isFOC = false;
                                    $itemData->remove_check_cost_price = false;

                                } else {
                                    $itemData->price_with_gst = $itemData->price;
                                    $itemData->isChecked = true;
                                    $itemData->isNew = false;
                                    $itemData->isFOC = $itemData->is_FOC == 0 ? false : true;
                                    $itemData->description = $itemData->name;
                                    $itemData->sub_description = $itemData->sub_description;
                                    // Get parent id from quotation template item
                                    $itemData->parent_id = $itemData->quotation_items->parent_id;
                                    $itemData->remove_check_cost_price = isset($itemDataArray[$itemId]) && $itemDataArray[$itemId]->document_id;

                                }
                                $item->id = isset($itemData->quotation_template_item_id) ? $itemData->quotation_template_item_id : $itemData->id;
                                $item->quantity = $itemData->quantity;
                                $item->lengthmm = $itemData->length;
                                $item->breadthmm = $itemData->breadth;
                                $item->heightmm = $itemData->height;
                                $item->parent_id = $itemData->parent_id;
                                $item->description = $itemData->description;
                                $item->sub_description = $itemData->sub_description;
                                $item->price_with_gst = $itemData->price_with_gst;
                                $item->cost_price = $itemData->cost_price;
                                $item->profit_margin = $itemData->profit_margin;
                                $item->unit_of_measurement = $itemData->unit_of_measurement;
                                $item->is_fixed_measurement = $itemData->is_fixed_measurement;
                                $item->isChecked = $itemData->isChecked;
                                $item->isNew = $itemData->isNew;
                                $item->isFOC = $itemData->isFOC;
                                $item->original_cost_price = $itemData->quotation_items ? $itemData->quotation_items->cost_price : $itemData->cost_price; // add value from template items for FE checking
                                $item->original_profit_margin = $itemData->quotation_items ? $itemData->quotation_items->profit_margin : $itemData->profit_margin; // add value from template items for FE checking
                                $item->remove_check_cost_price = $itemData->remove_check_cost_price ?? false;


                                //Add item to items array
                                $items[] = $item;
                            }
                        }
                        $items = $this->buildItemHierarchy($items);

                        //Pass items array to aow in data
                        $aow->items = $items;

                        //assign aow to aows array
                        $aows[] = $aow;
                    }
                }
                //Assign aows to section
                $section->area_of_works = $aows;

                $data->sections[] = $section;
            }
        }

        $version = '';
        if (!$documentDetail->signed_date) {
            $version = '/QO' . $documentDetail->version_number;
        }

        //get signed customers
        $customers = [];
        if(isset($documentDetail->customer_signatures))
        {
            foreach ($documentDetail->customer_signatures as $customer) {
                $carbonDate = Carbon::parse($customer->created_at);
                $formattedDate = $carbonDate->format('d/m/Y');
                // return $formattedDate;
                $customer_file_path = 'renovation_document/customer_signature_file/' . $customer->customer_signature;
                $customer_image = Storage::disk('public')->get($customer_file_path);
                $obj = new stdClass;
                $obj->customer_signature = base64_encode($customer_image);
                $obj->customer_name = $customer->customer->name_prefix . ' ' . $customer->customer->first_name . ' ' . $customer->customer->last_name;
                $obj->customer_nric = $customer->customer->customers->nric;
                $obj->signed_date = $formattedDate;
                array_push($customers, $obj);
            }
        }
        $formattedDate = "";
        if($documentDetail->signed_date){
            $formattedDate = $formattedDate = Carbon::parse($documentDetail->signed_date)->format('d/m/Y');
        }
        // $data->version_number = $documentDetail->version_number;
        $data->version_number = $version;
        $data->special_discount_percentage = $documentDetail->special_discount_percentage;
        $data->total_amount = $documentDetail->total_amount;
        $data->additional_notes = $documentDetail->additional_notes;
        $data->disclaimer = $documentDetail->disclaimer;
        $data->header_text = $documentStandard->header_text;
        $data->footer_text = $documentStandard->footer_text;
        $data->saleperson_signature = $saleperson_base64Image;
        $data->customers = $customers;
        $data->our_ref = $documentDetail?->projects?->agreement_no;
        $data->agreement_no = $documentDetail->agreement_no;
        $data->signed_date = $formattedDate;
        return $data;
    }

    private function buildItemHierarchy($items)
    {
        $itemMap = [];
        $rootItems = [];
        // First pass: create item map
        foreach ($items as $item) {
            $itemData = (object) [
                'id' => $item->id,
                'parent_id' => $item->parent_id,
                'quantity' => $item->quantity,
                'lengthmm' => $item->lengthmm,
                'breadthmm' => $item->breadthmm,
                'heightmm' => $item->heightmm,
                'description' => $item->description,
                'price_with_gst' => $item->price_with_gst,
                'cost_price' => $item->cost_price,
                'profit_margin' => $item->profit_margin,
                'unit_of_measurement' => $item->unit_of_measurement,
                'is_fixed_measurement' => $item->is_fixed_measurement,
                'isChecked' => $item->isChecked,
                'isNew' => $item->isNew,
                'isFOC' => $item->isFOC,
                'original_cost_price' => $item->original_cost_price ? $item->original_cost_price : 0, // add value from template items for FE checking
                'original_profit_margin' => $item->original_profit_margin ? $item->original_profit_margin : 0, // add value from template items for FE checking
                'sub_description' => $item->sub_description,
                'remove_check_cost_price' => $item->remove_check_cost_price ?? false,
                'items' => []
            ];
            $itemMap[$item->id] = $itemData;
        }

        // Second pass: assign children to their parents
        foreach ($itemMap as $item) {
            if ($item->parent_id && isset($itemMap[$item->parent_id])) {
                $itemMap[$item->parent_id]->items[] = $item;
            } else {
                $rootItems[] = $item;  // Top-level items
            }
        }

        return $rootItems;
    }

    public function sendEmailCopy($projectId, $email, $attachment)
    {
        $project = ProjectEloquentModel::find($projectId);

        $companyName = $project->company->name;

        $projectName = $project->property->street_name;

        $customer = $project->customer;

        $customerName = $customer->first_name . " " . $customer->last_name;

        $authUser = auth('sanctum')->user();

        $salespersonName = $authUser->first_name . "" . $authUser->last_name;

        Mail::to($email)->send(new SendEmailCopy($companyName, $projectName, $customerName, $salespersonName, $attachment));

        return true;
    }

    public function changeLeadStatus($renoDocumentId)
    {
        $renovationDocument = RenovationDocumentsEloquentModel::with('projects.customer')->find($renoDocumentId);

        if ($renovationDocument) {

            // $projectId = $renovationDocument->project_id;
            // $userId = $renovationDocument->projects->customer->id;
            $customerIds = $renovationDocument->customer_signatures()->pluck('customer_id');

            $customer = CustomerEloquentModel::whereIn('user_id', $customerIds);

            $customer->update(['status' => 2]);
        }

        return $renovationDocument;
    }

    public function updateInvoiceStartNumber($company_id, $invoice_no_start)
    {
        $checkCommonProjectNumSetting = GeneralSettingEloquentModel::where('setting', "enable_common_project_running_number")
                ->where('value', "true")
                ->first();

        if($checkCommonProjectNumSetting){
            $commonPjNum = GeneralSettingEloquentModel::where('setting','common_project_start_number')->first();
            $commonPjNum->increment('value');
            // $commonPjNum->value = $invoice_no_start;
            // $commonPjNum->save();
        }else{
            $companyEloquent = CompanyEloquentModel::where('id', $company_id)->first();
            $companyEloquent->invoice_no_start = $invoice_no_start;
            $companyEloquent->save();
        }

        return true;
    }

    private function buildNestedItems($items){
        $itemsById = [];
        $nestedItems = [];


        // First pass: Build a lookup array of items by their id
        foreach ($items as $item) {
            if (isset($item->renovation_area_of_work->name))
            $areaOfWorkName = $item->renovation_area_of_work->name;
        else{
            $areaOfWorkName = $item->renovation_item_area_of_work_id ? $item->renovation_area_of_work->areaOfWork->name : '';
        }

            $itemsById[$item->quotation_template_item_id] = [
                'id' => $item->id,
                'name' => $item->name,
                'calculation_type' => $item->renovation_sections->sections->calculation_type,
                'quantity' => $item->quantity,
                'current_quantity' => isset($item->current_quantity) ? $item->current_quantity : null,
                'lengthmm' => $item->length,
                'breadthmm' => $item->breadth,
                'heightmm' => $item->height,
                'measurement' => $item->unit_of_measurement,
                'is_fixed_measurement' => $item->is_fixed_measurement,
                'price' => $item->price,
                'cost_price' => $item->cost_price,
                'profit_margin' => $item->profit_margin,
                'is_FOC' => $item->is_FOC,
                'section_id' => $item->renovation_item_section_id,
                'section_name' => $item->renovation_sections->sections->name,
                'renovation_sections' => $item->renovation_sections,
                'renovation_area_of_work' => $item->renovation_area_of_work,
                'renovation_item_area_of_work_id' => $item->renovation_item_area_of_work_id,
                'area_of_work_name' => $areaOfWorkName, // Assuming this is correctly set elsewhere
                'is_excluded' => $item->is_excluded,
                'sub_description' => $item->sub_description,
                'items' => []
            ];
        }

        // Second pass: Build the nested structure
        foreach ($items as $item) {
            if ($item->parent_id === null) {
                // This is a parent item
                $nestedItems[] = &$itemsById[$item->quotation_template_item_id];
            } else {
                // This is a child item
                if (isset($itemsById[$item->parent_id])) {
                    $itemsById[$item->parent_id]['items'][] = &$itemsById[$item->quotation_template_item_id];
                }
            }
        }

        return $nestedItems;
    }

    public function signedQuotationDocument($project_id){
        $reno_data = RenovationDocumentsEloquentModel::query()->where('project_id',$project_id)->where('type','QUOTATION')->whereNotNull('signed_date')->first();

        return $reno_data;
    }

    public function customerSignRenoDocument($data){

        $customerSignatureFile = '';
        $renodocument = '';

        if (($data['type'] == 'QUOTATION' || $data['type'] == 'VARIATIONORDER' || $data['type'] == 'FOC' || $data['type'] == 'CANCELLATION') && isset($data['customer_signature']) && !$data['customer_signature'] instanceof UploadedFile) {
            if (isset($data['customer_signature'])) {
                $customer_signature_array = $data['customer_signature'];
                if (count($customer_signature_array) > 0) {
                    $initialFile = null;
                    foreach ($customer_signature_array as $customer_sign) {
                        $timestamp = time();
                        $uniqueId = uniqid();
                        $extension = $customer_sign['customer_signature']->extension();
                        $customerFileName = "{$timestamp}_{$uniqueId}.{$extension}";
                        $customerFilePath = 'renovation_document/customer_signature_file/' . $customerFileName;

                        Storage::disk('public')->put($customerFilePath, file_get_contents($customer_sign['customer_signature']));

                        $customerSignatureFile = $customerFileName;

                        $renovation_document_sign = RenovationDocumentsEloquentModel::find($data['id']);
                        $renovation_document_sign->customer_signatures()->create([
                            'renovation_id' => $data['id'],
                            'customer_id' => $customer_sign['customer_id'],
                            'customer_signature' => $customerSignatureFile
                        ]);
                        $initialFile = $customerSignatureFile;
                    }
                    $renovation_document_sign->update([
                        'customer_signature' => $initialFile,
                        'signed_date' => Carbon::now(),
                    ]);

                    $renodocument = $renovation_document_sign;

                }
            }
        } else {

            if (isset($data['customer_signature'])) {
                $customerFileName =  time() . '.' . $data['customer_signature']->extension();

                $customerFilePath = 'renovation_document/customer_signature_file/' . $customerFileName;

                Storage::disk('public')->put($customerFilePath, file_get_contents($data['customer_signature']));

                $customerSignatureFile = $customerFileName;
            }
            $renovation_document_sign = RenovationDocumentsEloquentModel::find($data['id']);

            $renodocument = RenovationDocumentsEloquentModel::where('id', $data['id'])->update([
                'customer_signature' => $customerSignatureFile,
                'signed_date' => Carbon::now()
            ]);
            $renodocument = $renovation_document_sign;
        }

        return $renodocument;
    }

    public function deleteQO($document_id)
    {

        DB::transaction(function () use ($document_id) {

            RenovationItemsEloquentModel::where('renovation_document_id', $document_id)->delete();

            RenovationSectionsEloquentModel::where('document_id', $document_id)->delete();

            RenovationAreaOfWorkEloquentModel::where('document_id', $document_id)->delete();

            RenovationDocumentsEloquentModel::find($document_id)->delete();

            SectionsEloquentModel::where('document_id', $document_id)->delete();

            SectionAreaOfWorkEloquentModel::where('document_id', $document_id)->delete();

            QuotationTemplateItemsEloquentModel::where('document_id', $document_id)->delete();

            SectionsIndexEloquentModel::where('document_id', $document_id)->delete();

            AOWIndexEloquentModel::where('document_id', $document_id)->delete();

            ItemsIndexEloquentModel::where('document_id', $document_id)->delete();

            RenovationSettingEloquentModel::where('renovation_document_id', $document_id)->delete();

        });

    }

    public function getPendingRenoDoc($filters)
    {
        $renoDocLists = RenovationDocumentsEloquentModel::query()
        ->with(
            'projects.properties',
            'projects.customers',
            'projects.salespersons.roles',
            'projects.saleReport.customer_payments.paymentType'
        )
        ->whereNotNull('salesperson_signature')
        ->where('salesperson_signature', '!=', '')
        ->where(function ($query) {
            $query->where('status', 'pending')
            ->orWhere('status', '1st_approved');
        })
        ->whereIn('id', function ($subquery) {
            $subquery->select(DB::raw('MAX(id)'))
            ->from('renovation_documents')
            ->groupBy('project_id', 'type');
        });

        $authUser = auth('sanctum')->user();
        if ($authUser->roles->contains('name', 'Manager')) {
            $staff = $authUser->staffs;
            $renoDocLists->whereHas('projects',function($query) use($staff){
                $query->whereHas('salespersons',function($query) use($staff){
                    $query->whereIn('users.id', function ($subQuery) use ($staff) {
                        $subQuery->select('user_id')
                        ->from('staffs')
                        ->where('mgr_id', $staff->user_id);
                    });
                });
            });
        }
        $renoDocResult = $renoDocLists->paginate($filters['perPage']);


        $finalResults = PendingRenoDocResource::collection($renoDocResult);

        $links = [
            'first' => $finalResults->url(1),
            'last' => $finalResults->url($finalResults->lastPage()),
            'prev' => $finalResults->previousPageUrl(),
            'next' => $finalResults->nextPageUrl(),
        ];

        $meta = [
            'current_page' => $finalResults->currentPage(),
            'from' => $finalResults->firstItem(),
            'last_page' => $finalResults->lastPage(),
            'path' => $finalResults->url($finalResults->currentPage()),
            'per_page' => $filters['perPage'],
            'to' => $finalResults->lastItem(),
            'total' => $finalResults->total(),
        ];

        $responseData['data'] = $finalResults;
        $responseData['links'] = $links;
        $responseData['meta'] = $meta;

        return $responseData;
    }

    public function updateQuotationDetail($document_id, $data)
    {
        $document = RenovationDocumentsEloquentModel::find($document_id);
        if($document){
            $signed_date =  Carbon::createFromFormat('d/m/Y', $data['date'])->format('Y-m-d');
            $document->update([
                "signed_date" => $signed_date ?? null,
                "agreement_no" => $data['agrNo']
            ]);

            $document->projects->update([
                "agreement_no" => $data['ourRef']
            ]);
        }
    }

}
