<?php

namespace Src\Company\Document\Presentation\API;

use DateTime;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Src\Company\Document\Application\Mappers\EvoMapper;
use Src\Company\Document\Application\Mappers\EvoItemMapper;
use Src\Company\Document\Application\UseCases\Commands\StoreEvoItemCommand;
use Src\Company\Document\Application\UseCases\Commands\StoreEvoCommand;
use Src\Company\Document\Application\UseCases\Queries\FindEvoByProjectIdQuery;
use Symfony\Component\HttpFoundation\Response;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\Document\Application\Requests\StoreEvoTemplateRequest;
use Src\Company\Document\Application\UseCases\Commands\SignEVOCommand;
use Src\Company\Document\Application\UseCases\Queries\FindEVOByIdQuery;
use Src\Company\Document\Application\UseCases\Queries\GetDocumentStandard;
use Src\Company\Document\Infrastructure\EloquentModels\EvoEloquentModel;
use Src\Company\Document\Application\Policies\EVOPolicy;
use Src\Company\Document\Application\UseCases\Queries\FindAllEvoTemplateItemQuery;
use Src\Company\Document\Application\UseCases\Queries\FindAllEvoTemplateRoomQuery;
use Src\Company\Document\Application\UseCases\Queries\FindEvoByIdForUpdate;
use Src\Company\Project\Application\UseCases\Queries\FindProjectByIdQuery;
use Src\Company\Project\Infrastructure\EloquentModels\ProjectEloquentModel;
use Src\Company\System\Application\UseCases\Queries\FindGeneralSettingByNameQuery;
use stdClass;

class EvoController extends Controller
{
    public function countLists($projectId)
    {
        abort_if(authorize('view', EVOPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for EVO!');

        try {

            $countLists = EvoEloquentModel::where('project_id', $projectId)->count();

            return response()->success($countLists, 'success', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function edit(int $id)
    {
        abort_if(authorize('update', EVOPolicy::class), Response::HTTP_FORBIDDEN, 'Need update permission for EVO!');

        try {
            $evoData = (new FindEvoByIdForUpdate($id))->handle();
            return response()->success($evoData, 'success', Response::HTTP_CREATED);
        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function getEvoByProjectId(int $projectId): JsonResponse
    {
        abort_if(authorize('view', EVOPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for EVO!');

        try {

            $items = (new FindEvoByProjectIdQuery($projectId))->handle();

            return response()->success($items, 'success', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function show(int $id): JsonResponse
    {
        abort_if(authorize('view', EVOPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for EVO!');

        try {
            return response()->json((new FindEVOByIdQuery($id))->handle());
        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function store(Request $request): JsonResponse
    {


        abort_if(authorize('store', EVOPolicy::class), Response::HTTP_FORBIDDEN, 'Need store permission for EVO!');

        try {

            $evo = EvoMapper::fromRequest($request);

            $evoData = (new StoreEvoCommand($evo))->execute();

            $items = json_decode($request->items);

            $evoId = $evoData->id;

            $evoItems = EvoItemMapper::fromRequest($items);

            $itemData = (new StoreEvoItemCommand($evoItems, $evoId))->execute();
            $request['project_id'] = $request->project_id;
            $request['evo_id'] = $evoId;
            $this->downloadPdf($request);
            return response()->success($itemData, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function sign(Request $request)
    {

        abort_if(authorize('sign', EVOPolicy::class), Response::HTTP_FORBIDDEN, 'Need sign permission for EVO!');

        try {

            (new SignEVOCommand($request))->execute();
            $request['project_id'] = $request->project_id;
            $request['evo_id'] = $request->id;
            $this->downloadPdf($request);
            return response()->success(null, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function getDocumentStandard(int $project_id): JsonResponse
    {

        try {
            return response()->json((new GetDocumentStandard($project_id))->handle());
        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
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
    public function getEvoDetails($itemLists, $roomLists, $projectData, $detailItems)
    {
        // Assuming these are all collections or stdClass objects
        // Directly setting the properties without `.value`
        $projectDataCollect = collect($projectData);
        $detailItemsCollect = collect($detailItems);


        $itemLists = collect($itemLists);
        $roomLists = collect($roomLists);
        $footerText = $detailItemsCollect['footer_text'];
        $disclaimer = $detailItemsCollect['disclaimer'];
        $versionNum = $detailItemsCollect['version_num'];
        $customerSignatureImage = $detailItemsCollect['customer_image'];

        $salepersonSignatureImage = $detailItemsCollect['saleperson_image'];
        $saleperson = new stdClass;
        $saleperson->name = $detailItemsCollect['signed_saleperson'];
        $saleperson->phoneNo = $detailItemsCollect['signed_sale_ph'];
        $saleperson->rank = $detailItemsCollect['rank'];

        $customer = new stdClass;
        $customer->name = $projectDataCollect['customers']->name;
        $customer->nric = $projectDataCollect['customers']->nric;
        $customer->signed_date = $detailItemsCollect['signed_date'];
        $checking = [];
        // return $itemLists;
        // Process evo_items
        collect($detailItemsCollect['evo_items'])->each(function ($item) use (&$itemLists, &$roomLists, &$checking) {
            $checkStatus = $itemLists->contains(function ($i) use ($item) {
                return $i->item_name == $item->item_description;
            });

            if ($checkStatus) {
                $findItem = $itemLists->firstWhere('item_name', $item->item_description);


                $findItem->total_qty = $item->quantity;
                $findItem->unit_rate = $item->unit_rate;
                $findItem->total_amount = $item->total;


                collect($item->rooms)->each(function ($d) use ($findItem) {
                    $findRoom = collect($findItem->rooms)->firstWhere('room_id', $d->pivot->room_id);
                    if ($findRoom) {
                        $findRoom->room_qty = $d->pivot->quantity;
                    }
                });
            } else {

                $obj = new stdClass;
                $obj->item_id = $item->id;
                $obj->item_name = $item->item_description;
                $obj->total_qty = $item->quantity;
                $obj->unit_rate = $item->unit_rate;
                $obj->total_amount = $item->total;
                $obj->rooms = collect($roomLists)->map(function ($room) use ($item) {
                    $roomObj = new stdClass;
                    $roomObj->room_id = $room->id;
                    $roomObj->room_name = $room->room_name;
                    $roomObj->room_qty = '';
                    return $roomObj;
                })->all();

                collect($item->rooms)->each(function ($i) use (&$obj) {
                    $r = collect($obj->rooms)->firstWhere('room_id', $i->pivot->room_id);
                    if ($r) {
                        $r->room_qty = $i->pivot->quantity;
                    }
                });

                $itemLists->push($obj);
            }
        });

        // Update room names based on the first item's rooms
        if (isset($detailItemsCollect['evo_items'][0])) {
            collect($detailItemsCollect['evo_items'][0]->rooms)->each(function ($item) use (&$roomLists) {
                $foundIndex = $roomLists->search(function ($data) use ($item) {
                    return $data->id === $item->room_id;
                });
                if ($foundIndex !== false) {
                    $roomLists[$foundIndex]->room_name = $item->room_name;
                }
            });
        }
        // return $checking;
        $totalAllAmount = $detailItemsCollect['total_amount'];
        $gstAmount = $totalAllAmount * ($projectDataCollect['companies']->gst_percentage / 100);
        $grandTotal = $detailItemsCollect['grand_total'];

        // Prepare the return data
        $returnData = new stdClass;
        $returnData->itemLists = $itemLists;
        $returnData->roomLists = $roomLists;
        $returnData->gst_percentage = $projectDataCollect['companies']->gst_percentage;
        // Assuming you might need these for something later
        $returnData->footerText = $footerText;
        $returnData->disclaimer = $disclaimer;
        $returnData->versionNum = $versionNum;
        $returnData->customerSignatureImage = $customerSignatureImage;
        $returnData->salepersonSignatureImage = $salepersonSignatureImage;
        $returnData->saleperson = $saleperson;
        $returnData->customer = $customer;
        $returnData->totalAllAmount = $totalAllAmount;
        $returnData->gstAmount = $gstAmount;
        $returnData->grandTotal = $grandTotal;

        return $returnData;
    }
    public function getTemplateItems($items, $roomLists, $projectData)
    {
        $projectDataCollect = collect($projectData);
        $itemLists = collect();

        foreach ($items as $item) {
            $obj = new \stdClass();
            $obj->item_id = $item->id;
            $obj->item_name = $item->description;
            $obj->total_qty = 0;

            // Assuming $projectData is an object with the necessary structure.
            $obj->unit_rate = $projectDataCollect['companies']->gst_reg_no
                ? $item->unit_rate_with_gst
                : $item->unit_rate_without_gst;

            $obj->total_amount = 0;
            $obj->rooms = collect();

            foreach ($roomLists as $room) {
                $roomObj = new \stdClass();
                $roomObj->room_id = $room->id;
                $roomObj->room_name = $room->room_name;
                $roomObj->room_qty = '';
                $obj->rooms->push($roomObj);
            }

            $itemLists->push($obj);
        }

        // Convert the collection to an array if necessary
        return $itemLists->toArray();
    }
    function changeFormatDate($dateString)
    {
        try {
            $date = new DateTime($dateString);
            return $date->format('d/m/Y');
        } catch (\Exception $e) {
            // Handle exception if the date cannot be parsed
            return null;
        }
    }
    public function downloadPdf(Request $request)
    {
        $evo_id = $request->evo_id;
        $project_id = $request->project_id;
        $detailItems = (new FindEVOByIdQuery($evo_id))->handle();
        // return $detailItems;
        $project = (new FindProjectByIdQuery($project_id))->handle();
        $roomLists = (new FindAllEvoTemplateRoomQuery())->handle();
        $items = (new FindAllEvoTemplateItemQuery())->handle();
        $itemLists = $this->getTemplateItems($items, $roomLists, $project);

        $evoDetail = $this->getEvoDetails($itemLists, $roomLists, $project, $detailItems);
        $enable_show_last_name_first = (new FindGeneralSettingByNameQuery('enable_show_last_name_first'))->handle();


        $customers_array = $project->customersPivot->toArray();
        $folder_name  = env('COMPANY_FOLDER_NAME', 'Twp');
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
            "tel" => $project->company->tel,
            "fax" => $project->company->fax
        ];
        $headerFooterData = [
            "footer" => $evoDetail->footerText,
            "companies" => $companies,
            "customers" => $customers,
            "properties" => $properties,
            "signed_date" => $detailItems->signed_date ? $this->changeFormatDate($detailItems->signed_date) : null,
            "created_at" => $detailItems->created_at ? $this->changeFormatDate($detailItems->created_at) : null,
            "our_ref" => $project->agreement_no,
            "agr" => "$project->agreement_no/EVO$detailItems->version_num",
            "folder_name" => $folder_name,
            "customers_array" => $customers_array,
            'signed_sale_email' => $detailItems->salesperson->user->email,
            'signed_sale_ph' => $detailItems->salesperson->user->contact_no,
            'signed_saleperson' => $detailItems->salesperson->user->first_name . ' ' . $detailItems->salesperson->user->last_name,
            'enable_show_last_name_first' => $enable_show_last_name_first->value

        ];

        $gstAmount = $evoDetail->totalAllAmount  == 0 ? 0 : $evoDetail->totalAllAmount * ($evoDetail->gst_percentage / 100);

        // return $evoDetail;
        $headerHtml = view('pdf.Common.evoHeader', $headerFooterData)->render();
        $footerHtml = view('pdf.Common.footer', $headerFooterData)->render();
        $data = [
            'roomLists' => $evoDetail->roomLists,
            'itemLists' => $evoDetail->itemLists,
            'grandTotal' => $evoDetail->grandTotal,
            'gstAmount' => $gstAmount,
            'saleperson' => $evoDetail->saleperson,
            'totalAllAmount' => $evoDetail->totalAllAmount,
            'versionNum' => $evoDetail->versionNum,
            'disclaimer' => $evoDetail->disclaimer,
            'salepersonSignatureImage' => $evoDetail->salepersonSignatureImage,
            'customerSignatureImage' => $evoDetail->customerSignatureImage,
            'customer' => $evoDetail->customer,
            'gst_percentage' => $evoDetail->gst_percentage,
            'signed_date' => $detailItems->signed_date,
            'company_name' => $companies['name'],
            'quotationData' => $headerFooterData,
            'is_artdecor' => config('folder.company_folder_name') == 'Artdecor' ? true : false,
        ];
        $pdf = \PDF::loadView('pdf.EVO.evo', $data);
        $pdf->setOption('enable-javascript', true);
        $pdf->setOption('header-html', $headerHtml);
        $pdf->setOption('footer-html', $footerHtml);
        if ($folder_name == 'Miracle') {

            $pdf->setOption('margin-top', 65);
        } else if ($folder_name == 'Artdecor') {
            $pdf->setOption('margin-top', 10);
            $pdf->setOption('header-html', "");
        } else {
            $pdf->setOption('margin-top', 60);
        }
        $pdf->setOption('margin-bottom', 40);

        $pdfDocument = EvoEloquentModel::find($evo_id);
        $fileName = 'evo_' . time() . '.pdf';
        $filePath = 'pdfs/' . $fileName;
        Storage::disk('public')->put($filePath, $pdf->output());

        // Store the file path in the database (assuming you have a model PdfDocument)
        if ($pdfDocument) {
            // Check if the old PDF file exists and delete it
            if (!empty($pdfDocument->pdf_file) && Storage::disk('public')->exists('pdfs/' . $pdfDocument->pdf_file)) {
                Storage::disk('public')->delete('pdfs/' . $pdfDocument->pdf_file);
            }
            // Update the database with the new file name
            $pdfDocument->update([
                'pdf_file' => $fileName
            ]);
        }
        return $pdf->download("EVO version" . $detailItems->version_num . ".pdf");
    }
}
