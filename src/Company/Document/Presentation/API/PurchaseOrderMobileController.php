<?php

namespace Src\Company\Document\Presentation\API;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Src\Common\Infrastructure\Laravel\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Src\Company\Document\Application\Mappers\PurchaseOrderItemMapper;
use Src\Company\Document\Application\Mappers\PurchaseOrderMapper;
use Src\Company\Document\Application\Policies\PurchaseOrderPolicy;
use Src\Company\Document\Application\Requests\UpdatePurchaseOrderRequest;
use Src\Company\Document\Application\UseCases\Commands\DeletePurchaseOrderItemMobileCommand;
use Src\Company\Document\Application\UseCases\Commands\ManagerSignPurchaseOrderMobileCommand;
use Src\Company\Document\Application\UseCases\Commands\StorePurchaseOrderItemMobileCommand;
use Src\Company\Document\Application\UseCases\Commands\StorePurchaseOrderMobileCommand;
use Src\Company\Document\Application\UseCases\Commands\UpdatePOWithItemsMobileCommand;
use Src\Company\Document\Application\UseCases\Queries\FindAllPurchaseOrderMobileQuery;
use Src\Company\Document\Application\UseCases\Queries\FindPurchaseOrderByIdMobileQuery;
use Src\Company\Document\Application\UseCases\Queries\FindPurchaseOrderByProjectIdMobileQuery;
use Src\Company\Document\Application\UseCases\Queries\GetCompanyStampByProjectIdMobileQuery;
use Src\Company\Document\Application\UseCases\Queries\GetPurchaseOrderNumberMobileCount;
use Src\Company\Document\Infrastructure\EloquentModels\PurchaseOrderEloquentModel;
use Src\Company\Project\Application\UseCases\Queries\FindProjectByIdMobileQuery;

class PurchaseOrderMobileController extends Controller
{
    public function index(Request $request)
    {
        abort_if(authorize('view', PurchaseOrderPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Purchase Order!');

        try {

            $filters = $request->all();

            $purchaseOrders = (new FindAllPurchaseOrderMobileQuery($filters))->handle();

            return response()->success($purchaseOrders, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function managerSign(Request $request)
    {
        abort_if(authorize('sign', PurchaseOrderPolicy::class), Response::HTTP_FORBIDDEN, 'Need sign permission for Purchase Order!');

        try {

            (new ManagerSignPurchaseOrderMobileCommand($request))->execute();
            $request['po_id'] = $request->id;
            $this->downloadPdf($request);

            return response()->success(null, 'Purchase Order Approve Successful !', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function poListByProjectId($projectId)
    {
        try {
            $purchaseOrders = (new FindPurchaseOrderByProjectIdMobileQuery($projectId, false))->handle();

            return response()->success($purchaseOrders, 'success', Response::HTTP_OK);
        } catch (Exception $ex) {
            return response()->error($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);

        }
    }

    public function poShow($id): JsonResponse
    {
        // abort_if(authorize('view', PurchaseOrderPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Purchase Order!');

        try {

            $po = (new FindPurchaseOrderByIdMobileQuery($id))->handle();

            return response()->success($po, 'success', Response::HTTP_OK);
        } catch (Exception $ex) {
            return response()->error($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);

        }
    }

    public function store(Request $request)
    {
        // abort_if(authorize('store', PurchaseOrderPolicy::class), Response::HTTP_FORBIDDEN, 'Need store permission for Purchase Order!');
        DB::beginTransaction();
        try {
            $po = PurchaseOrderMapper::fromRequest($request);

            $poData = (new StorePurchaseOrderMobileCommand($po))->execute();

            $poId = $poData->id;

            $itemRequests = $request->items;

            $poItems = PurchaseOrderItemMapper::fromRequest(json_decode($itemRequests));

            (new StorePurchaseOrderItemMobileCommand($poItems, $poId))->execute();
            $request['po_id'] = $poId;
            $this->downloadPdf($request);
            DB::commit();
            return response()->success($itemRequests, 'Purchase Order Create Successful !', Response::HTTP_CREATED);
        } catch (Exception $ex) {
            DB::rollBack();
            return response()->error($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(int $id, UpdatePurchaseOrderRequest $request): JsonResponse
    {
        // abort_if(authorize('update', PurchaseOrderPolicy::class), Response::HTTP_FORBIDDEN, 'Need update permission for Purchase Order!');

        try {

            $po = (new UpdatePOWithItemsMobileCommand($request, $id))->execute();
            $request['po_id'] = $id;
            $this->downloadPdf($request);
            return response()->success($po, 'Purchase Order Update Successful !', Response::HTTP_OK);
        } catch (Exception $ex) {
            DB::rollBack();
            return response()->error($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function downloadPdf(Request $request)
    {
        $po_id = $request->po_id;
        $projectId = $request->project_id;
        $folder_name  = env('COMPANY_FOLDER_NAME', 'Twp');
        $project = (new FindProjectByIdMobileQuery($projectId))->handle();
        $poData = (new FindPurchaseOrderByIdMobileQuery($po_id))->handle();
        $poCollectData = collect($poData['po']);
        $poFooterCollectData = collect($poData['poFooter']);
        $stamp = (new GetCompanyStampByProjectIdMobileQuery($projectId))->handle();

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

        $data = [
            "poData" => $poCollectData,
            "companyStamp" => $stamp,
            "folder_name" => $folder_name,
            "company_logo" => $this->getCompanyLogo($project->company->logo),
            "companies" => $companies,
            'is_artdecor' => config('folder.company_folder_name') == 'Artdecor' ? true : false,

        ];
        $headerFooterData = [
            'footer' => $poFooterCollectData['footer_text']
        ];
        $pdf = \PDF::loadView('pdf.PURCHASEORDER.purchase_order', $data);
        $footerHtml = view('pdf.Common.footer', $headerFooterData)->render();
        $pdf->setOption('margin-bottom', 30);
        $pdf->setOption('footer-html', $footerHtml);
        $pdfDocument = PurchaseOrderEloquentModel::find($po_id);
        $fileName = 'purchase_order_' . time() . '.pdf';
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
        return $pdf->download("PURCHASE ORDER.pdf");
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

    public function purchaseOrderCount()
    {
        // abort_if(authorize('view', PurchaseOrderPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Purchase Order!');

        try {

            $purchaseOrderCount = (new GetPurchaseOrderNumberMobileCount())->handle();

            return response()->success($purchaseOrderCount, 'success', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function poCountByProjectId($projectId): JsonResponse
    {
        abort_if(authorize('view', PurchaseOrderPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Purchase Order!');

        try {

            $pos = (new FindPurchaseOrderByProjectIdMobileQuery($projectId, true))->handle();

            return response()->success($pos, 'success', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function destroyItem(int $id): JsonResponse
    {
        abort_if(authorize('destroy_item', PurchaseOrderPolicy::class), Response::HTTP_FORBIDDEN, 'Need destroy_item permission for Purchase Order!');

        try {
            (new DeletePurchaseOrderItemMobileCommand($id))->execute();

            return response()->success($id, "Successfully Deleted", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

}
