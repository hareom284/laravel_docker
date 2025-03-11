<?php

namespace Src\Company\Document\Presentation\API;

use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Src\Common\Infrastructure\Laravel\Controller;
use Symfony\Component\HttpFoundation\Response;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Src\Company\CustomerManagement\Infrastructure\EloquentModels\CustomerEloquentModel;
use Src\Company\Document\Application\Mappers\ContractMapper;
use Src\Company\Document\Application\Mappers\RenovationDocumentsMapper;
use Src\Company\Document\Application\Policies\ProjectQuotationPolicy;
use Src\Company\Document\Application\UseCases\Commands\ChangeLeadStatusMobileCommand;
use Src\Company\Document\Application\UseCases\Commands\StoreContractMobileCommand;
use Src\Company\Document\Application\UseCases\Commands\StoreRenovationCustomerSignMobileCommand;
use Src\Company\Document\Application\UseCases\Commands\StoreRenovationDocumentMobileCommand;
use Src\Company\Document\Application\UseCases\Commands\StoreRenovationItemScheduleByDocumentIdMobileCommand;
use Src\Company\Document\Application\UseCases\Commands\UpdateCompanyInvoiceStartNoMobileCommand;
use Src\Company\Document\Application\UseCases\Queries\FindAllRenovationDocumentsMobileQuery;
use Src\Company\Document\Application\UseCases\Queries\FindRenovationDocumentsIndexMobileQuery;
use Src\Company\Document\Application\UseCases\Queries\FindTemplateItemsForUpdateMobileQuery;
use Src\Company\Document\Domain\Mail\NotifyDocumentSignEmail;
use Src\Company\Document\Infrastructure\EloquentModels\AOWIndexEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\ContractEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\ItemsIndexEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\RenovationDocumentsEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\RenovationItemsEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\RenovationSectionsEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\RenovationSettingEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\SectionsIndexEloquentModel;
use Src\Company\Project\Application\UseCases\Commands\StoreTermAndConditionSignaturesMobileCommand;
use Src\Company\Project\Application\UseCases\Queries\FindProjectByIdMobileQuery;
use Src\Company\Project\Infrastructure\EloquentModels\ProjectEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\SaleReportEloquentModel;
use Src\Company\System\Application\UseCases\Queries\FindGeneralSettingByNameQuery;
use Src\Company\System\Infrastructure\EloquentModels\GeneralSettingEloquentModel;


class RenovationDocumentMobileController extends Controller
{
    protected $original_quantities;

    public function index($project_id, $renovation_type)
    {

        $data = (new FindRenovationDocumentsIndexMobileQuery($project_id, $renovation_type))->handle();

        return response()->success($data, 'success', Response::HTTP_OK);
    }

    public function show($id, $type)
    {
        abort_if(authorize('view', ProjectQuotationPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Renovation Document!');

        try {

            $renovation_documents_data = (new FindAllRenovationDocumentsMobileQuery($id, $type))->handle();

            return response()->success($renovation_documents_data, 'success', Response::HTTP_OK);
            // return response()->json($renovation_documents_data, Response::HTTP_OK);
        } catch (\Exception $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $data = json_decode($request->data);
            $renovation_documents = RenovationDocumentsMapper::fromRequest($request, $request->reno_document_id);
            $renovation_documents_data = (new StoreRenovationDocumentMobileCommand($renovation_documents, $data))->execute();
            $request['renovation_document_id'] = $renovation_documents_data->id;
            $request['project_id'] = $request->project_id;
            $request['pdf_status'] = "SAVE";
            $request['type'] = $request->type;
            $this->downloadPdf($request);
            DB::commit();
            return response()->json($renovation_documents_data, Response::HTTP_CREATED);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->error(null, $e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function templateForUpdate($renovation_document_id)
    {
        // abort_if(authorize('store', ProjectQuotationPolicy::class), Response::HTTP_FORBIDDEN, 'Need store permission for Renovation Document!');

        try {
            $result = (new FindTemplateItemsForUpdateMobileQuery($renovation_document_id))->handle();

            return response()->success($result, 'success', Response::HTTP_OK);
            // return response()->json($result, Response::HTTP_CREATED);
        } catch (\Exception $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function getSignedRenoDoc()
    {
        try {

            $signedRenoDoc = [
                [
                    'agreement_no' => 'DDSO/1024/777/1/QO',
                    'created_at' => '2024-10-28',
                    'price' => '11881.00'
                ],
                [
                    'agreement_no' => 'DDSO/1024/777/1/VO',
                    'created_at' => '2024-10-28',
                    'price' => '1000.00'
                ],
                [
                    'agreement_no' => 'DDSO/1024/777/1/CN',
                    'created_at' => '2024-10-28',
                    'price' => '-100.00'
                ]
            ];

            return response()->success($signedRenoDoc, 'Success', Response::HTTP_OK);

        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function getApprovedPayment($projectId)
    {
        try {

            // Initialize the data array
            $data = [];

            $evoAmts = $this->evoRepositoryInterface->getEvoAmt($projectId);

            if (count($evoAmts) !== 0) {

                foreach ($evoAmts as $index => $evoAmt) {
                    $data[] = [
                        'name' => "EVO" . $index + 1,
                        'version' => $evoAmt->projects->agreement_no.'/EVO'.$evoAmt->version_number,
                        'total_amount' => number_format($evoAmt->total_amount, 2, '.', ','),
                        'signed_date' => $evoAmt->signed_date
                    ];
                }
            }

            $modifiedDocs = $this->renovationDocumentInterface->getConfirmAmtsByProjectId($projectId);

            // Add the modifiedDocs to the data array
            if($modifiedDocs){

                foreach ($modifiedDocs as $doc) {

                    if($doc['name'] === "QUOTATION"){

                        $data[] = [
                            'name' => "Contract",
                            'version' => $doc['version'],
                            'signed_date' => $doc['signed_date'],
                            'total_amount' => $doc['total_amount']
                        ];

                    }else{

                        $data[] = [
                            'name' => $doc['name'],
                            'version' => $doc['version'],
                            'signed_date' => $doc['signed_date'],
                            'total_amount' => $doc['total_amount']
                        ];
                    }

                }

            }

            $results['amounts'] = $data;

            return response()->success($results, 'success', Response::HTTP_OK);

            // $approvedPayment = [
            //     "amounts" => [
            //         [
            //             "name" => "Contract",
            //             "version" => "DD\/SO\/1124\/252\/54",
            //             "signed_date" => "2024-11-28",
            //             "total_amount" => "1,199.00"
            //         ],
            //         [
            //             "name" => "VARIATIONORDER",
            //             "version" => "DD\/SO\/1124\/252\/54\/VO1",
            //             "signed_date" => "2024-11-28",
            //             "total_amount" => "-1,199.00"
            //         ],
            //         [
            //             "name" => "VARIATIONORDER 1",
            //             "version" => "DD\/SO\/1124\/252\/54\/VO2",
            //             "signed_date" => "2024-11-28",
            //             "total_amount" => "-1,199.00"
            //         ]
            //     ]
            // ];

            // return response()->success($approvedPayment, 'Success', Response::HTTP_OK);

        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function sign(Request $request)
    {
        abort_if(authorize('sign', ProjectQuotationPolicy::class), Response::HTTP_FORBIDDEN, 'Need sign permission for Renovation Document!');

        DB::beginTransaction();
        try {

            (new StoreRenovationCustomerSignMobileCommand($request->all()))->execute();

            (new ChangeLeadStatusMobileCommand($request->id))->execute();

            $renovation_document = RenovationDocumentsEloquentModel::with('renovation_items')->where('id', $request->id)->first();

            $renovation_document->renovation_items()->update(['active' => true]);

            $projectId = RenovationDocumentsEloquentModel::find($request->id)->project_id;

            $project = ProjectEloquentModel::find($projectId);

            $documentTypeShortName = '';
            $checkCommonQuotationNumSetting = GeneralSettingEloquentModel::where('setting', "enable_common_quotation_running_number")
                ->where('value', "true")
                ->first();
            $checkCommonProjectNumSetting = GeneralSettingEloquentModel::where('setting', "enable_common_project_running_number")
            ->where('value', "true")
            ->first();

            $common_project_running_number = 0;
            $common_quotation_running_number = 0;
            if ($checkCommonProjectNumSetting) {
                $commonPjNum = GeneralSettingEloquentModel::where('setting', 'common_project_start_number')->first();
                $common_project_running_number = $commonPjNum->value;
            }

            if ($checkCommonQuotationNumSetting) {
                $commonQONum = GeneralSettingEloquentModel::where('setting', 'common_quotation_start_number')->first();
                $common_quotation_running_number = $commonQONum->value;
            }

            $runningNum = $project->company->invoice_no_start;

            $salesperson = $project->salespersons->first();
            $name = $salesperson->first_name . ' ' . $salesperson->last_name;

            $initialSalespersonName = implode("", array_map(fn($word) => $word[0], explode(" ", $name)));
            $block_num = $project->property->block_num;
            $block_num = str_replace(array('Blk', 'Blk ', 'Block', 'Block ', 'blk', 'blk ', 'BLK', 'BLK ', 'BLOCK', 'BLOCK '), '', $block_num);

            $documentNumber = 0;

            switch ($request->type) {
                case 'QUOTATION':

                    $documentTypeShortName = 'QO';

                    $projectAgreementNum = generateAgreementNumber('signed_project', [
                        'company_initial' => $project->company->docu_prefix,
                        'salesperson_initial' => $initialSalespersonName,
                        'block_num' => $block_num,
                        'date' => Carbon::now()->toDateString(),
                        'running_num' => $runningNum,
                        'project_id' => $project->id,
                        'quotation_num' => $project->company->quotation_no,
                        'project_agr_no' => $project->agreement_no,
                        'common_project_running_number' => $common_project_running_number,
                        'common_quotation_running_number' => $common_quotation_running_number
                    ]);

                    //end change

                    $project->update([
                        'invoice_no' => $runningNum,
                        'agreement_no' => $projectAgreementNum,
                        'project_status' => "InProgress"
                    ]);

                    (new UpdateCompanyInvoiceStartNoMobileCommand($project->company_id, ++$runningNum))->execute();

                    $contracts = ContractEloquentModel::where('project_id', $projectId)->exists();

                    if ($contracts) {
                        ContractEloquentModel::where('project_id', $projectId)->update([
                            'contract_sum' => $renovation_document->total_amount,
                        ]);
                    }

                    $projectSaleReport = SaleReportEloquentModel::where('project_id', $projectId)->first();

                    if(is_null($projectSaleReport)){

                        SaleReportEloquentModel::create([
                            'project_id' => $projectId,
                            'total_sales' => $renovation_document->total_amount,
                            'remaining' => $renovation_document->total_amount
                        ]);

                    }else{

                        $projectSaleReport->update([
                            'total_sales' => $renovation_document->total_amount,
                            'remaining' => $renovation_document->total_amount
                        ]);
                    }

                    foreach ($renovation_document->renovation_items as $item) {
                        if (!$item->prev_item_id) {
                            RenovationSectionsEloquentModel::find($item->renovation_item_section_id)->increment('total_items_count');
                        }
                    }

                    if(isset($request->customer_signature[0]['customer_id'])){
                        $customer = CustomerEloquentModel::where('user_id', $request->customer_signature[0]['customer_id'])->first();

                        $customer->update([
                            'quote_value' => $renovation_document->total_amount,
                            'book_value' => $renovation_document->total_amount,
                        ]);
                    } else {
                        $project = ProjectEloquentModel::with('properties', 'salespersons', 'customer')->find($projectId);
                        $customer = CustomerEloquentModel::where('user_id', $project->customer_id)->first();
                        $customer->update([
                            'quote_value' => $renovation_document->total_amount,
                            'book_value' => $renovation_document->total_amount,
                        ]);
                    }

                    $companyName = config('folder.company_folder_name');

                    if ($companyName == 'Tag' || $companyName == 'Aplus') {
                        $contractData = new Request();
                        $contractData->project_id = $project->id;
                        $contractData->name = $customer->user->first_name . ' ' . $customer->user->last_name;
                        $contractData->nric = $customer->nric;
                        $contractData->company = $project->company->name;
                        $contractData->law = '10 WOODLANDS SECTOR 2 S(737 727)';
                        $contractData->contractor_days = 0;
                        $contractData->termination_days = 0;
                        $contractData->address = $project->properties->block_num . ' ' . $project->properties->street_name . ' #' . $project->properties->unit_num . ' Singapore ' . $project->properties->postal_code;

                        $contract = ContractMapper::fromRequest($contractData);

                        $result = (new StoreContractMobileCommand($contract))->execute();
                        $enableMultipleTermAndCondition = GeneralSettingEloquentModel::where('setting', "enable_multiple_term_and_conditions")
                        ->where('value', "true")
                        ->first();
                        if($enableMultipleTermAndCondition){
                        $termAndConditions = (new StoreTermAndConditionSignaturesMobileCommand($result))->execute();
                        }
                        $contractController = new ContractController();
                        $contractController->downloadContractPdf($contractData);
                    }

                    break;

                case 'VARIATIONORDER':
                    $documentNumber = RenovationDocumentsEloquentModel::where('project_id', $projectId)->whereNotNull('signed_date')->where('type', 'VARIATIONORDER')->count();

                    $documentTypeShortName = 'VO';

                    $saleReport = SaleReportEloquentModel::where('project_id', $projectId)->first(['total_sales', 'remaining']);

                    SaleReportEloquentModel::where('project_id', $projectId)->update([
                        'total_sales' => $saleReport->total_sales + $renovation_document->total_amount,
                        'remaining' => $saleReport->remaining + $renovation_document->total_amount
                    ]);

                    foreach ($renovation_document->renovation_items as $item) {
                        if (!$item->prev_item_id) {
                            RenovationSectionsEloquentModel::find($item->renovation_item_section_id)->increment('total_items_count');
                        }
                    }

                    break;

                case 'FOC':
                    $documentNumber = RenovationDocumentsEloquentModel::where('project_id', $projectId)->whereNotNull('signed_date')->where('type', 'FOC')->count();

                    $documentTypeShortName = 'FOC';

                    foreach ($renovation_document->renovation_items as $item) {
                        if (!$item->prev_item_id) {
                            RenovationSectionsEloquentModel::find($item->renovation_item_section_id)->increment('total_items_count');
                        }
                    }

                    break;

                case 'CANCELLATION':
                    $documentNumber = RenovationDocumentsEloquentModel::where('project_id', $projectId)->whereNotNull('signed_date')->where('type', 'CANCELLATION')->count();

                    $documentTypeShortName = 'CN';

                    $saleReport = SaleReportEloquentModel::where('project_id', $projectId)->first(['total_sales', 'remaining']);

                    SaleReportEloquentModel::where('project_id', $projectId)->update([
                        'total_sales' => $saleReport->total_sales - $renovation_document->total_amount,
                        'remaining' => $saleReport->remaining - $renovation_document->total_amount
                    ]);

                    foreach ($renovation_document->renovation_items as $item) {
                        RenovationSectionsEloquentModel::find($item->renovation_item_section_id)->increment('total_items_count');
                    }

                default:
                    # code...
                    break;
            }

            (new StoreRenovationItemScheduleByDocumentIdMobileCommand($request->id))->execute();
            $request['renovation_document_id'] = $renovation_document->id;
            $request['project_id'] = $projectId;
            $request['pdf_status'] = "SAVE";
            $request['type'] = $request->type;

            if($documentTypeShortName === 'QO'){
                $agreementNum = $projectAgreementNum;
            }else{
                $agreementNum = generateAgreementNumber('signed_renovation_document', [
                    'company_initial' => $project->company->docu_prefix,
                    'salesperson_initial' => $initialSalespersonName,
                    'block_num' => $project->property->block_num ?? null,
                    'date' => Carbon::now()->toDateString(),
                    'document_type' => $documentTypeShortName . ($documentNumber > 0 ? $documentNumber : ''),
                    'running_num' => $project->company->invoice_no_start,
                    'version_num' => $renovation_document->version_number ?? '',
                    'project_id' => $project->id,
                    'quotation_num' => $project->company->quotation_no,
                    'project_agr_no' => $project->agreement_no,
                    'common_project_running_number' => $common_project_running_number,
                    'common_quotation_running_number' => $common_quotation_running_number
                ]);
            }

            $renovation_document->agreement_no = $agreementNum;

            $renovation_document->save();

            $this->downloadPdf($request);

            $mailgun = config('services.mailgun.secret');

            if (isset($mailgun)) {
                $project = ProjectEloquentModel::with('properties', 'salespersons', 'customer')->find($projectId);

                foreach ($project->salespersons as $saleperson) {

                    $emailData = [
                        'address' => $project->properties->block_num . ' ' . $project->properties->street_name . ' #' . $project->properties->unit_num . ' Singapore ' . $project->properties->postal_code,
                        'customer' => $project->customer->first_name . ' ' . $project->customer->last_name,
                        'type' => $request->type,
                        'saleperson' => $saleperson->first_name . ' ' . $saleperson->last_name,
                    ];

                    Mail::to($saleperson->email)->send(new NotifyDocumentSignEmail($emailData));
                }
            }
            DB::commit();

            return response()->success('', "Reno Doc Sign Successful !", Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            logger('message',[$e->getTrace()]);
            return response()->error(null, $e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function downloadPdf(Request $request)
    {
        $id = $request->renovation_document_id;
        $project_id = $request->project_id;

        $printable = $request->printable;

        // $folder_name = $request->folder_name;
        $current_folder_name = config('folder.company_folder_name');
        $folder_name  = "";
        // $current_folder_name == 'Magnum' ? 'Twp' : $current_folder_name;
        if ($current_folder_name == 'Default') {
            $folder_name = 'Twp';
        } else if ($current_folder_name == 'Magnum') {
            $folder_name = 'Twp';
        } else if ($current_folder_name == 'Miracle') {
            $folder_name = 'Twp';
        } else if ($current_folder_name == 'Jream') {
            $folder_name = 'Twp';
        } else if ($current_folder_name == 'Paddry') {
            $folder_name = 'Twp';
        } else if ($current_folder_name == 'Molecule') {
            $folder_name = 'Twp';
        } else if ($current_folder_name == 'Henglai') {
            $folder_name = 'Twp';
        } else if ($current_folder_name == 'Optimum'){
            $folder_name = 'Twp';
        } else if ($current_folder_name == 'FiveFoot10'){
            $folder_name = 'Twp';
        }
        else {
            $folder_name = $current_folder_name;
        }

        $doc_type = $request->type;
        $project = (new FindProjectByIdMobileQuery($project_id))->handle();
        $customers_array = $project->customersPivot->toArray();
        $quotationLists = (new FindAllRenovationDocumentsMobileQuery($id, $doc_type))->handle();
        $sortData = $this->sortingOfQuotation($id, $doc_type);

        $version = '';
        $docTypeShortName = '';
        switch ($doc_type) {
            case 'QUOTATION':
                $version = '/QO';
                $docTypeShortName = 'QO';
                break;

            case 'VARIATIONORDER':
                $version = '/VO';
                $docTypeShortName = 'VO';
                break;

            case 'FOC':
                $version = '/FOC';
                $docTypeShortName = 'FOC';
                break;

            case 'CANCELLATION':
                $version = '/CN';
                $docTypeShortName = 'CN';
                break;

            default:
                # code...
                break;
        }

        if ($doc_type != 'QUOTATION') {
            $version_number = RenovationDocumentsEloquentModel::where('project_id', $project_id)
                ->where('type', $doc_type)
                ->get();

            foreach ($version_number as $key => $value) {

                if ($id == $value->id) {
                    $version .= (string)$key + 1;
                }
            }
        } else {
            if ($quotationLists->ver != null && $quotationLists->ver == 1) {
                $version .= $quotationLists->ver;
            } else {
                $version = $quotationLists->signed_date ? ' ' : $version . $quotationLists->ver;
            }
        }
        $enable_only_show_discount_amount = (new FindGeneralSettingByNameQuery('enable_only_show_discount_amount'))->handle();
        $enable_show_last_name_first = (new FindGeneralSettingByNameQuery('enable_show_last_name_first'))->handle();
        $enable_payment_terms = (new FindGeneralSettingByNameQuery('enable_payment_terms'))->handle();
        $enable_cn_in_vo_status = (new FindGeneralSettingByNameQuery('enable_cn_in_vo'))->handle();
        $enable_show_selling_price = (new FindGeneralSettingByNameQuery('enable_show_selling_price_in_quotation_summary'))->handle();

        $properties = [
            "id" => $project->properties->id,
            "street_name" => $project->properties->street_name,
            "unit_num" => $project->properties->unit_num,
            "block_num" => $project->properties->block_num,
            "postal_code" => $project->properties->postal_code,
            "type_id" => $project->properties->propertyType->id,
            "type" => $project->properties->propertyType->type,
        ];

        $customers = [
            "id" => $project->customers->id,
            "name" => $project->customers->name_prefix . ' ' . $project->customers->first_name . ' ' . $project->customers->last_name,
            "email" => $project->customers->email,
            "contact_no" => $project->customers->contact_no,
            "nric" => $project->customers->customers->nric ?? "",
            "profile_pic" => $project->customers->profile_pic ? asset('storage/profile_pic/' . $project->customers->profile_pic) : null,
            "company_name" => $project->customers->customers->company_name,
            "customer_type" => $project->customers->customers->customer_type
        ];

        $companies = [
            "id" => $project->company->id,
            "name" => $project->company->name,
            "email" => $project->company->email,
            "hdb_license_no" =>  $project->company->hdb_license_no,
            "reg_no" => $project->company->reg_no,
            "gst_reg_no" => $project->company->gst_reg_no,
            "gst_percentage" => $project->company->gst_reg_no ? $project->company->gst : 0,
            "main_office" => $project->company->main_office,
            "company_logo" => $this->getCompanyLogo($project->company->logo),
            "company_stamp" => $this->getCompanyStamp($project->company->company_stamp),
            "tel" => $project->company->tel,
            "fax" => $project->company->fax
        ];

        // If quotation does not have agr no, display a default one in PDF
        if (empty($quotationLists->agreement_no))
            $agreementNo = $project->agreement_no . $version;
        else {
            $agreementNo = $quotationLists->agreement_no;
            if($project->project_status == 'InProgress' && $doc_type == 'QUOTATION'){
                // $agreementNo = preg_replace('/\/QO(?:[-\/]?\d+)?$/', '', $agreementNo);
                $agreementNo = $project->agreement_no;
            }
        }

        $headerFooterData = [
            'header' => $quotationLists->header_text,
            'footer' => $quotationLists->footer_text,
            'saleperson_signature' => $quotationLists->salesperson_signature,
            'signed_date' => $quotationLists->signed_date ? $this->changeFormatDate($quotationLists->signed_date) : null,
            'signed_sale_email' => $quotationLists->signed_sale_email,
            'signed_sale_ph' => $quotationLists->signed_sale_ph,
            'signed_saleperson' => $quotationLists->signed_saleperson,
            // 'version_num' => $quotationLists->version_num,
            'version_num' => $version,
            'rank' => $quotationLists->rank,
            'disclaimer' => $quotationLists->disclaimer,
            'terms' => $quotationLists->terms,
            'customer_signature' => $quotationLists->customer_signature,
            'created_at' => $quotationLists->created_at ? $this->changeFormatDate($quotationLists->created_at) : null,
            'already_sign' => $quotationLists->already_sign,
            'project' => $project,
            'document_agreement_no' => $agreementNo,
            'customers_array' => $customers_array,
            'customers' => $customers,
            'properties' => $properties,
            'companies' => $companies,
            'folder_name' => $current_folder_name,
            'saleperson_id' => $quotationLists->saleperson_id,
            'customer_ids' => $quotationLists->customer_ids,
            'doc_type' => $doc_type,
            'enable_show_last_name_first' => $enable_show_last_name_first->value,
            'created_at_supaspace' => $quotationLists->created_at ? $this->convertDateSupaspace($quotationLists->created_at) : null,
            'signed_date_supaspace' => $quotationLists->signed_date ? $this->convertDateSupaspace($quotationLists->signed_date) : null,
            'salepersonRegistryNo' => $quotationLists->salepersonRegistry,
            'status' => $quotationLists->status
        ];

        if ($doc_type == 'VARIATIONORDER') {
            $total_prices = $this->calculateTotalAllAmountVO($quotationLists, $companies['gst_percentage']);
        } else {
            $total_prices = $this->calculateTotalAllAmount($quotationLists, $companies['gst_percentage']);
        }

        $hide_total_exist_status = RenovationSettingEloquentModel::where([
            'renovation_document_id' => $id,
            'setting' => 'hide_total_in_print'
        ])->value('value') === 'true';

        $data = [
            'sortQuotation' => $sortData,
            'quotationList' => $quotationLists,
            'quotationData' => $headerFooterData,
            'total_prices' => $total_prices,
            'folder_name' => $folder_name,
            'doc_type'  => $doc_type,
            'hide_total' => $hide_total_exist_status,
            'current_folder_name' => $current_folder_name,
            'original_quantities' => isset($this->original_quantities) ? $this->original_quantities : [],
            'customers_array' => $project->customersPivot->toArray(),
            'settings' => [
                'enable_only_show_discount_amount' => $enable_only_show_discount_amount->value,
                'enable_show_last_name_first' => $enable_show_last_name_first->value,
                'enable_payment_terms' => $enable_payment_terms->value,
                'enable_cn_in_vo' => $enable_cn_in_vo_status->value === 'true',
                'enable_show_selling_price' => $enable_show_selling_price->value,
            ]
        ];

        if($doc_type == 'VARIATIONORDER' && GeneralSettingEloquentModel::where('setting', 'enable_cn_in_vo')->exists())
        {

            $enable_cn_in_vo = $enable_cn_in_vo_status->value === 'true';

            if ($enable_cn_in_vo) {
                $sectionWithCNData = [
                    'sectionWithoutCNItems' => $this->convertSectionWithOrWithoutCN($sortData, 0),
                    'sectionWithCNItems' => $this->convertSectionWithOrWithoutCN($sortData, 1)
                ];
                $data['sectionWithCNData'] = $sectionWithCNData;
                // $data['sectionWithCNItems'] = $this->convertSectionWithOrWithoutCN($sortData, 1);
                // $data['sectionWithoutCNItems'] = $this->convertSectionWithOrWithoutCN($sortData, 0);
            }

        }

        // return $data;

        $file = '';
        switch ($doc_type) {
            case 'QUOTATION':
                $file = '.quotation';
                break;
            case 'VARIATIONORDER':
                $file = '.variation_order';
                break;
            case 'CANCELLATION':
                $file = '.cancellation';
                break;
            case 'FOC':
                $file = '.foc';
                break;
            default:
                $file = '.quotation';
                break;
        }

        $headerHtml = view('pdf.Common.header', $headerFooterData)->render();
        $footerHtml = view('pdf.Common.footer', $headerFooterData)->render();

        try {
            $pdf = \PDF::loadView('pdf.' . $doc_type . '.' . $folder_name . $file, $data);
            // Log::channel('daily')->info('pdf.' . $doc_type . '.' . $folder_name . $file);
            $pdf->setOption('enable-javascript', true);
            if ($folder_name == 'Twp' && $current_folder_name == 'Miracle') {
                $pdf->setOption('margin-top', 108);
            } else if ($folder_name == 'Twp' && $current_folder_name == 'Henglai') {
                $pdf->setOption('margin-top', 85);
            } else if ($folder_name == 'Xcepcion') {
                $pdf->setOption('margin-top', 80);
            } else if ($folder_name == 'Metis') {
                $pdf->setOption('margin-top', 100);
            } else if ($folder_name == 'Artdecor') {
                $pdf->setOption('margin-top', 10);
            } else if ($folder_name == 'BuildSpec') {
                $pdf->setOption('margin-top', 40);
            } else if ($folder_name == 'Luxcraft') {
                $pdf->setOption('margin-top', 85);
            } else if ($folder_name == 'Makegood') {
                $pdf->setOption('margin-top', 10);
            } else if ($folder_name == 'Amp') {
                $pdf->setOption('margin-top', 80);
            } else if ($folder_name == 'Tag') {
                $pdf->setOption('margin-top', 65);
            } else if ($folder_name == 'Flynn') {
                $pdf->setOption('margin-top', 45);
            } else if ($folder_name == 'Brightway') {
                $pdf->setOption('margin-top', 5);
            } else if ($folder_name == 'Twp' && $current_folder_name == 'Molecule') {
                $pdf->setOption('margin-top', 95);
            } else if ($folder_name == 'Twp' && $current_folder_name == 'Henglai') {
                $pdf->setOption('margin-top', 95);
            } else if ($folder_name == 'Supaspace') {
                $pdf->setOption('margin-top', 90);
            } else if ($folder_name == 'Tidplus') {
                $pdf->setOption('margin-top', 57);
            } else if ($folder_name == 'Whst') {
                $pdf->setOption('margin-top', 55);
            } else if ($folder_name == 'Optimum') {
                $pdf->setOption('margin-top', 80);
            } else if ($folder_name == 'Blackalogy') {
                $pdf->setOption('margin-top', 75);
            } else if ($folder_name == 'SaltRoom'){
                $pdf->setOption('margin-top', 5);
            } else if ($folder_name == 'Praxis'){
                $pdf->setOption('margin-top', 15);
            } else if ($folder_name == 'Aplus') {
                $pdf->setOption('margin-top', 10);
            }
            else {
                $pdf->setOption('margin-top', 85);
            }


            if ($folder_name == 'Twp') {
                $pdf->setOption('margin-bottom', 40);
                $pdf->setOption('header-html', $headerHtml);
                $pdf->setOption('footer-html', $footerHtml);
                // $pdf->addPage('pdf.Common.lastPageComponent',[
                //     'terms' => isset($quotationLists->terms) ? $quotationLists->terms : null
                // ]);
            } else if ($folder_name == 'Xcepcion') {
                $headerXceHtml = view('pdf.Common.xcepcionHeader', $headerFooterData)->render();
                $footerXceHtml = view('pdf.Common.xcepcionFooter', $headerFooterData)->render();
                $pdf->setOption('margin-bottom', 10);
                $pdf->setOption('header-html', $headerXceHtml);
                $pdf->setOption('footer-html', $footerXceHtml);
            } else if ($folder_name == 'Metis') {
                $metisFooterData = [
                    'quotationData' => $headerFooterData
                ];
                $headerMetHtml = view('pdf.Common.metisHeader', $headerFooterData)->render();
                $footerMetHtml = view('pdf.Common.metisFooter', $metisFooterData)->render();
                $pdf->setOption('margin-bottom', 40);
                $pdf->setOption('margin-left', 15);
                $pdf->setOption('margin-right', 15);
                $pdf->setOption('header-html', $headerMetHtml);
                $pdf->setOption('footer-html', $footerMetHtml);
            } else if ($folder_name == 'Artdecor') {
                $pdf->setOption('margin-bottom', 6);
                $footerArtHtml = view('pdf.Common.artdecorFooter')->render();
                $pdf->setOption('footer-html', $footerArtHtml);
            } else if ($folder_name == 'BuildSpec') {
                $headerBSHtml = view('pdf.Common.buildspecHeader', $headerFooterData)->render();
                $footerBSHtml = view('pdf.Common.buildspecFooter', $headerFooterData)->render();
                $pdf->setOption('margin-bottom', 40);
                $pdf->setOption('header-html', $headerBSHtml);
                $pdf->setOption('footer-html', $footerBSHtml);
            } else if ($folder_name == 'Luxcraft') {
                $metisFooterData = [
                    'quotationData' => $headerFooterData
                ];
                $headerMetHtml = view('pdf.Common.luxcraftHeader', $headerFooterData)->render();
                $footerMetHtml = view('pdf.Common.luxcraftFooter', $metisFooterData)->render();
                $pdf->setOption('margin-bottom', 40);
                $pdf->setOption('margin-left', 15);
                $pdf->setOption('margin-right', 15);
                $pdf->setOption('header-html', $headerMetHtml);
                $pdf->setOption('footer-html', $footerMetHtml);
            } else if ($folder_name == 'Makegood') {
                $pdf->setOption('header-html', "");
                $pdf->setOption('margin-left', 5);
                $pdf->setOption('margin-right', 5);
            } else if ($folder_name == 'Amp') {
                $ampFooterData = [
                    'quotationData' => $headerFooterData
                ];
                $headerAmpHtml = view('pdf.Common.Amp.header', $headerFooterData)->render();
                $footerAmpHtml = view('pdf.Common.Amp.footer', $ampFooterData)->render();
                $pdf->setOption('margin-bottom', 40);
                $pdf->setOption('margin-left', 15);
                $pdf->setOption('margin-right', 15);
                $pdf->setOption('header-html', $headerAmpHtml);
                $pdf->setOption('footer-html', $footerAmpHtml);
            } else if ($folder_name == 'Tag') {
                $pdf->setOption('margin-bottom', 6);
                $headerTagHtml = view('pdf.Common.Tag.header', $headerFooterData)->render();
                $footerTagHtml = view('pdf.Common.Tag.footer')->render();
                $pdf->setOption('footer-html', $footerTagHtml);
                $pdf->setOption('header-html', $headerTagHtml);
            } else if ($folder_name == 'Flynn') {
                $flynnFooterData = [
                    'quotationData' => $headerFooterData
                ];
                $pdf->setOption('margin-bottom', 35);
                $headerFlynnHtml = view('pdf.Common.Flynn.header', $headerFooterData)->render();
                $footerFlynnHtml = view('pdf.Common.Flynn.footer', $flynnFooterData)->render();
                $pdf->setOption('footer-html', $footerFlynnHtml);
                $pdf->setOption('header-html', $headerFlynnHtml);
            } else if ($folder_name == 'Brightway') {
                $pdf->setOption('margin-bottom', 5);
                $pdf->setOption('footer-html', '');
                $pdf->setOption('header-html', '');
            } else if ($folder_name == 'Supaspace') {
                $supaspaceFooterData = [
                    'quotationData' => $headerFooterData
                ];
                $pdf->setOption('margin-bottom', 20);
                $pdf->setOption('margin-left', 0);
                $pdf->setOption('margin-right', 0);
                $headerSupaspaceHtml = view('pdf.Common.Supaspace.header', $headerFooterData)->render();
                $footerSupaspaceHtml = view('pdf.Common.Supaspace.footer', $supaspaceFooterData)->render();
                $pdf->setOption('footer-html', $footerSupaspaceHtml);
                $pdf->setOption('header-html', $headerSupaspaceHtml);
            } else if ($folder_name == 'Tidplus') {
                $pdf->setOption('margin-bottom', 29);
                $pdf->setOption('margin-left', 0);
                $pdf->setOption('margin-right', 0);
                // $pdf->setOption('footer-html', '');
                // $pdf->setOption('header-html', '');
                $headerTidplusHtml = view('pdf.Common.Tidplus.header', $headerFooterData)->render();
                $footerTidplusHtml = view('pdf.Common.Tidplus.footer', $headerFooterData)->render();
                if($printable == 'true'){
                    $pdf->setOption('header-html', '');
                    $pdf->setOption('header-html', '');
                } else {
                    $pdf->setOption('footer-html', $footerTidplusHtml);
                    $pdf->setOption('header-html', $headerTidplusHtml);
                }
            } else if ($folder_name == 'Whst') {
                $pdf->setOption('margin-bottom', 18);
                $headerWhstHtml = view('pdf.Common.Whst.header', $headerFooterData)->render();
                $footerWhstHtml = view('pdf.Common.Whst.footer', $headerFooterData)->render();
                $pdf->setOption('margin-left', 15);
                $pdf->setOption('margin-right', 15);
                $pdf->setOption('footer-html', $footerWhstHtml);
                $pdf->setOption('header-html', $headerWhstHtml);
            } else if ($folder_name == 'Optimum') {
                $optimumFooterData = [
                    'quotationData' => $headerFooterData
                ];
                $pdf->setOption('margin-bottom', 50);
                $pdf->setOption('margin-left', 5);
                $pdf->setOption('margin-right', 5);

                $headerOptimumHtml = view('pdf.Common.Optimum.header', $headerFooterData)->render();
                $footerOptimumHtml = view('pdf.Common.Optimum.footer', $optimumFooterData)->render();
                $pdf->setOption('footer-html', $footerOptimumHtml);
                $pdf->setOption('header-html', $headerOptimumHtml);
            } else if ($folder_name == 'Blackalogy') {

                $blackalogyHeaderFooterData = [
                    'quotationData' => $headerFooterData
                ];

                $pdf->setOption('margin-bottom', 95);
                $pdf->setOption('margin-left', 5);
                $pdf->setOption('margin-right', 5);

                $headerBlackalogyHtml = view('pdf.Common.Blackalogy.header', $blackalogyHeaderFooterData)->render();
                $footerBlackalogyHtml = view('pdf.Common.Blackalogy.footer', $blackalogyHeaderFooterData)->render();
                $pdf->setOption('footer-html', $footerBlackalogyHtml);
                $pdf->setOption('header-html', $headerBlackalogyHtml);

            } else if ($folder_name == "SaltRoom") {

                $saltRoomHeaderFooterData = [
                    'quotationData' => $headerFooterData
                ];

                $footerSaltRoomHtml = view('pdf.Common.SaltRoom.footer', $saltRoomHeaderFooterData)->render();

                $pdf->setOption('margin-bottom', 10);
                $pdf->setOption('footer-html', $footerSaltRoomHtml);
                $pdf->setOption('header-html', '');

            } else if($folder_name == "Praxis") {
                $pdf->setOption('header-html', '');
                $pdf->setOption('header-html', '');
                $pdf->setOption('margin-bottom', 15);
                $pdf->setOption('margin-left', 17);
                $pdf->setOption('margin-right', 17);

            } else if ($folder_name == 'Aplus') {
                $pdf->setOption('header-html', '');
                $pdf->setOption('footer-html', '');
                $pdf->setOption('margin-top', 10);
                $pdf->setOption('margin-bottom', 10);
                $pdf->setOption('margin-left', 6);
                $pdf->setOption('margin-right', 6);
            } else {
                $pdf->setOption('header-html', $headerHtml);
            }

            $pdfDocument = RenovationDocumentsEloquentModel::find($id);
            switch ($doc_type) {
                case 'QUOTATION':
                    $fileName = 'quotation_' . time() . '.pdf';
                    break;
                case 'VARIATIONORDER':
                    $fileName = 'variation_' . time() . '.pdf';
                    break;
                case 'CANCELLATION':
                    $fileName = 'cancellation_' . time() . '.pdf';
                    break;
                case 'FOC':
                    $fileName = 'foc_' . time() . '.pdf';
                    break;
                default:
                    $fileName = 'quotation_' . time() . '.pdf';
                    break;
            }

            $termsContent = strip_tags($quotationLists->terms);
            if (($current_folder_name == 'Twp' || $current_folder_name == 'Jream' || $current_folder_name == 'Henglai') && !empty(trim($termsContent))) {
                try {
                    // Generate the first PDF
                    $pdf1Path = storage_path('app/temp_pdf1.pdf');
                    $pdf->save($pdf1Path);

                    // Generate the second PDF
                    $pdf2 = \PDF::loadView('pdf.Common.lastPageComponent', [
                        'current_folder_name' => $current_folder_name,
                        'terms' => $quotationLists->terms,
                        'quotationData' => $headerFooterData,
                        'customers_array' => $customers_array,
                        'settings' => [
                            'enable_only_show_discount_amount' => $enable_only_show_discount_amount->value,
                            'enable_show_last_name_first' => $enable_show_last_name_first->value
                        ]
                    ]);

                    $pdf2Path = storage_path('app/temp_pdf2.pdf');
                    $pdf2->setOption('margin-bottom', 20);
                    $pdf2->setOption('margin-top', 20);
                    $pdf2->setOption('header-html', "");
                    $pdf2->setOption('footer-html', "");
                    $pdf2->save($pdf2Path);

                    return $this->mergePdf($pdf1Path, $pdf2Path, $pdfDocument);
                } catch (\Exception $ex) {
                    // Check and delete temp files if they exist
                    if (file_exists(storage_path('app/temp_pdf1.pdf'))) {
                        unlink(storage_path('app/temp_pdf1.pdf'));
                    }
                    if (file_exists(storage_path('app/temp_pdf2.pdf'))) {
                        unlink(storage_path('app/temp_pdf2.pdf'));
                    }

                    // Rethrow the exception if you want it to be handled further up the stack
                    return $ex;
                }
            } else {

                $fileName = 'quotation_' . time() . '.pdf';
                // Save the PDF to a file in the 'public' disk
                $filePath = 'pdfs/' . $fileName;
                Storage::disk('public')->put($filePath, $pdf->output());

                // Store the file path in the database (assuming you have a model PdfDocument)
                if ($pdfDocument) {
                    // Check if the old PDF file exists and delete it
                    // Update the database with the new file name
                    if($printable === 'true'){

                        if (!empty($pdfDocument->printable_pdf) && Storage::disk('public')->exists('pdfs/' . $pdfDocument->printable_pdf)) {
                            Storage::disk('public')->delete('pdfs/' . $pdfDocument->printable_pdf);
                        }

                        $pdfDocument->update([
                            'printable_pdf' => $fileName
                        ]);
                    } else {

                        if (!empty($pdfDocument->pdf_file) && Storage::disk('public')->exists('pdfs/' . $pdfDocument->pdf_file)) {
                            Storage::disk('public')->delete('pdfs/' . $pdfDocument->pdf_file);
                        }

                        $pdfDocument->update([
                            'pdf_file' => $fileName
                        ]);
                    }
                }
                if (!$request->pdf_status && $request->pdf_status != 'SAVE') {
                    return $pdf->download("Quotation version" . $quotationLists->version_num . ".pdf");
                }
            }
        } catch (\Exception $th) {
            // Log::channel('daily')->error($th);
            return $th;
        }
    }

    function sortArrayBySequence($originalArray, $sequenceArray)
    {
        $indexedArray = array_combine(range(0, count($originalArray) - 1), $originalArray);

        $sortedAssocArray = [];
        foreach ($sequenceArray as $value) {
            $key = array_search($value, $indexedArray);
            if ($key !== false) {
                $sortedAssocArray[$key] = $value;
            }
        }

        return array_values($sortedAssocArray);
    }

    function sortingOfQuotation($id, $type)
    {
        $quotationLists = (new FindAllRenovationDocumentsMobileQuery($id, $type))->handle(); // Assuming $quotationLists is a collection and has an 'items' collection.

        //handle by wai yan check items quantity for variation order
        if ($type == 'VARIATIONORDER') {


            $projectId = $quotationLists->project_id;

            $sign_quotation_id = RenovationDocumentsEloquentModel::where('project_id', $projectId)
                ->where('type', 'QUOTATION')
                ->whereNotNull('signed_date')
                ->pluck('id')
                ->first();

            $lastSignedVO = RenovationDocumentsEloquentModel::where('project_id', $projectId)
                ->where('type', 'VARIATIONORDER')
                ->whereNotNull('signed_date')
                ->pluck('id')
                ->last();

            $original_signed_quotaiton_lists = (new FindAllRenovationDocumentsMobileQuery($sign_quotation_id, 'QUOTATION'))->handle();
            if ($lastSignedVO) {
                $renoItems = RenovationItemsEloquentModel::where('renovation_document_id', $lastSignedVO)
                    ->pluck('quantity', 'quotation_template_item_id')
                    ->toArray();
            } else {
                $renoItems = [];
            }

            $this->original_quantities = $original_signed_quotaiton_lists->renovation_items
                ->pluck('quantity', 'quotation_template_item_id')
                ->toArray();

            $this->original_quantities = $renoItems + $this->original_quantities;

            $items = $quotationLists->renovation_items;
            // logger('items',[$items]);

            $renovation_items = $items;
        } else {
            $renovation_items = $quotationLists->renovation_items;
        }


        //end handle

        // $renovation_items = $quotationLists->renovation_items; //old one
        $data = $renovation_items->map(function ($item) {
            $areaOfWorkName = isset($item->renovation_area_of_work->name)
                ? $item->renovation_area_of_work->name
                : ($item->renovation_item_area_of_work_id
                    ? $item->renovation_area_of_work->areaOfWork->name
                    : '');

            return [
                'id' => $item->id,
                'quotation_template_item_id' => $item->quotation_template_item_id,
                'parent_id' => $item->parent_id,
                'name' => $item->name,
                'calculation_type' => $item->renovation_sections->sections->calculation_type,
                'quantity' => $item->quantity,
                'current_quantity' => isset($item->current_quantity) ? $item->current_quantity : null,
                'length' => $item->length,
                'breadth' => $item->breadth,
                'height' => $item->height,
                'measurement' => $item->unit_of_measurement,
                'is_fixed_measurement' => $item->is_fixed_measurement,
                'price' => $item->price,
                'cost_price' => $item->cost_price,
                'profit_margin' => $item->profit_margin,
                'is_FOC' => $item->is_FOC,
                'is_CN' => $item->is_CN,
                'section_id' => $item->renovation_sections->section_id,
                'section_name' => $item->renovation_sections->name,
                'area_of_work_id' => $item->renovation_area_of_work->section_area_of_work_id,
                'area_of_work_name' => $areaOfWorkName,
                'is_excluded' => $item->is_excluded
            ];
        });

        $sortQuotation = [];
        // Getting unique section IDs. pluck() gets the values of a given key (column):
        $sectionIds = [];

        foreach ($data as $item) {
            $sectionIds[] = $item['section_id'];
        }

        $originalSections = collect($sectionIds)->unique()->toArray();
        $sectionIndexArray = SectionsIndexEloquentModel::where('document_id', $id)
            ->pluck('section_sequence')
            ->first();


        $sectionIndexArray = json_decode($sectionIndexArray);


        $uniqueSections = $this->sortArrayBySequence($originalSections, $sectionIndexArray);

        foreach ($uniqueSections as $sectionId) {
            $filteredItemsBySectionId = $data->filter(function ($item) use ($sectionId) {
                return $item['section_id'] == $sectionId;
            });


            $emptyAOWItems = $filteredItemsBySectionId->whereNull('area_of_work_id');
            $filterAOWItems = $filteredItemsBySectionId->whereNotNull('area_of_work_id');


            $originalAOWItems = $filterAOWItems->pluck('area_of_work_id')->unique()->toArray();


            $aowIndexData = AOWIndexEloquentModel::where('document_id', $id)
                ->where('section_id', $sectionId)
                ->pluck('aow_sequence')
                ->first();

            $aowIndexArray = json_decode($aowIndexData);
            $uniqueAOWIds = $this->sortArrayBySequence($originalAOWItems, $aowIndexArray);

            $hasAOWItems = [];


            $itemsIndexData = ItemsIndexEloquentModel::where('document_id', $id)
                ->whereIn('aow_id', $uniqueAOWIds)
                ->get()
                ->groupBy('aow_id');

            foreach ($uniqueAOWIds as $aowId) {

                $itemIndexArray = json_decode(optional($itemsIndexData->get($aowId)->first())->items_sequence ?? '[]');


                $aowItems = $filterAOWItems->filter(function ($item) use ($aowId) {
                    return $item['area_of_work_id'] == $aowId;
                })->sortBy(function ($item) use ($itemIndexArray) {
                    return array_search($item['quotation_template_item_id'], $itemIndexArray);
                });

                if ($aowItems->isNotEmpty()) {
                    $firstAowItem = $aowItems->first(); // Getting the first item to extract AOW details
                    $aowItems = $this->organizeItemsByParent($aowItems);
                    $objForAOW = [
                        'area_of_work_id' => $aowId,
                        'area_of_work_name' => $firstAowItem['area_of_work_name'],
                        // 'area_of_work_items' => $aowItems->values()->all(), // this is david's code
                        'area_of_work_items' => $aowItems,
                    ];
                    $hasAOWItems[] = $objForAOW;
                }
            }

            $sectionObj = [
                'section_id' => $sectionId,
                'section_name' => $filteredItemsBySectionId->first()['section_name'],
                'emptyAOWData' => $emptyAOWItems->values()->all(),
                'hasAOWData' => $hasAOWItems,
            ];

            $sortQuotation[] = $sectionObj;
        }

        return collect($sortQuotation);
    }

    function organizeItemsByParent($items)
    {
        $itemMap = [];
        $result = [];

        // First pass: organize items by ID and find top-level items.
        foreach ($items as $item) {
            $item['items'] = [];  // Initialize a sub-items array.
            $itemMap[$item['quotation_template_item_id']] = $item;
            if (!$item['parent_id']) {
                $result[] = &$itemMap[$item['quotation_template_item_id']];
            }
        }

        // Second pass: assign sub-items to their parents.
        foreach ($items as $item) {
            if ($item['parent_id'] && isset($itemMap[$item['parent_id']])) {
                $itemMap[$item['parent_id']]['items'][] = &$itemMap[$item['quotation_template_item_id']];
            }
        }

        return $result;
    }

    public function getCompanyLogo($company_logo)
    {
        if ($company_logo) {
            $customer_file_path = 'logo/' . $company_logo;

            $company_image = Storage::disk('public')->get($customer_file_path);

            $company_base64Image = base64_encode($company_image);
            return $company_base64Image;
        }
    }

    public function getCompanyStamp($companyStamp)
    {
        if ($companyStamp) {
            $customer_file_path = 'stamp/' . $companyStamp;

            $company_image = Storage::disk('public')->get($customer_file_path);

            $company_base64Image = base64_encode($company_image);
            return $company_base64Image;
        }
    }

    function changeFormatDate($dateString)
    {
        try {
            $date = new DateTime($dateString);
            return $date->format('d/m/Y');
        } catch (Exception $e) {
            return null;
        }
    }

    function convertDateSupaspace($dateString)
    {
        try {
            $date = new DateTime($dateString);
            return $date->format('F j, Y');
        } catch (Exception $e) {
            return null;
        }
    }

    public function calculateTotalAllAmountVO($quotationLists, $gst_percentage)
    {
        $percentage = $quotationLists->special_discount_percentage;
        $totalAllAmount = $quotationLists->section_total_amount->sum('total_price');
        $totalSpecialDiscount = $totalAllAmount * (100 - $percentage) / 100;
        $totalGST = $totalSpecialDiscount * ($gst_percentage / 100);

        $totalInclusive = $totalGST +  $totalSpecialDiscount;
        $only_discount_amount = $quotationLists->section_total_amount->sum('total_price') * (100 - $percentage) / 100;

        return [
            'gst_percentage' => $gst_percentage,
            'discount_percentage' => $percentage,
            'total_inclusive' => $totalInclusive,
            'total_gst' => $totalGST,
            'total_special_discount' => $totalSpecialDiscount,
            'total_all_amount' => $totalAllAmount,
            'only_discount_amount' => $only_discount_amount

        ];
    }

    function calculateTotalAllAmount($quotationLists, $gst_percentage)
    {
        $percentage = $quotationLists->special_discount_percentage;
        $sectionAllAmount = $quotationLists->section_total_amount;
        $totalAllAmountValue = 0;
        foreach ($sectionAllAmount as $i) {
            $totalAllAmountValue += floatval($i['total_price']);
        }

        $total_all_amount = $totalAllAmountValue;

        $total_special_discount =
            $total_all_amount == 0
            ? 0
            : $total_all_amount - $total_all_amount * (floatval($percentage) / 100);

        $total_gst =
            $total_all_amount == 0
            ? 0
            : $total_special_discount * ($gst_percentage / 100);

        $total_inclusive =
            $total_all_amount == 0
            ? 0
            : $total_special_discount + $total_gst;

        $only_discount_amount = $total_all_amount - $total_special_discount;
        return [
            'gst_percentage' => $gst_percentage,
            'discount_percentage' => $percentage,
            'total_all_amount' => $total_all_amount,
            'total_special_discount' => $total_special_discount,
            'total_gst' => $total_gst,
            'total_inclusive' => $total_inclusive,
            'only_discount_amount' => $only_discount_amount
        ];
    }

    function convertSectionWithOrWithoutCN($sortData, $num)
    {
        $sections = collect($sortData);

        $data = $sections->map(function ($section) use($num) {
            $filteredHasAOWData = collect($section['hasAOWData'])->map(function ($aow) use($num) {
                $aow['area_of_work_items'] = collect($aow['area_of_work_items'])
                                            ->map(fn($item) => $this->filterItemsRecursively($item, $num))
                                            ->filter()
                                            ->values()
                                            ->all();
                return $aow;
            })->filter(fn($aow) => !empty($aow['area_of_work_items']))->all();

            if (!empty($filteredHasAOWData)) {
                $section['hasAOWData'] = $filteredHasAOWData;
                $section['section_total_price'] = collect($filteredHasAOWData)
                ->flatMap(fn($aow) => $aow['area_of_work_items'])
                ->sum(function ($item) use($num) {
                    return $this->calculateItemTotal($item, $num);
                });

                return $section;
            }
        })->filter()->values();

        $sumOfTotalPrice = $data->sum('section_total_price');

        return [
            'data' => $data,
            'total' => $sumOfTotalPrice
        ];
    }

    private function filterItemsRecursively($item, $num)
    {
        $matchesCurrentItem = $item['is_CN'] == $num;

        if (isset($item['items']) && is_array($item['items'])) {
            $filteredSubItems = collect($item['items'])
                ->map(fn($nestedItem) => $this->filterItemsRecursively($nestedItem, $num))
                ->filter()
                ->values()
                ->all();

            $item['items'] = $filteredSubItems;

            if (count($filteredSubItems) > 0) {
                return $item;
            }

        }

        return $matchesCurrentItem ? $item : null;
    }

    private function calculateItemTotal($item, $num)
    {
        $totalSum = $item['quantity'] * $item['price'];

        $total = ($num && $totalSum < 0) || (!$num && $totalSum > 0) ? $totalSum : 0;

        if (isset($item['items']) && is_array($item['items'])) {

            $nestedTotal = collect($item['items'])->sum(function($nestedItem) use($num) {
                return $this->calculateItemTotal($nestedItem, $num);
            });

            $total += ($num && $nestedTotal < 0) || (!$num && $nestedTotal > 0) ? $nestedTotal : 0;
        }

        return $total;
    }

}
