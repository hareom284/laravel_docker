<?php

namespace Src\Company\Document\Presentation\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Http\Response;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\Document\Application\Requests\StorePurchaseOrderRequest;
use Src\Company\Document\Application\Requests\UpdatePurchaseOrderRequest;
use Src\Company\Document\Application\Requests\UpdatePurchaseOrderForSaleReportRequest;
use Src\Company\Document\Application\UseCases\Queries\FindPurchaseOrderByProjectId;
use Src\Company\Document\Application\Mappers\PurchaseOrderMapper;
use Src\Company\Document\Application\Mappers\PurchaseOrderItemMapper;
use Src\Company\Document\Application\UseCases\Commands\StorePurchaseOrderCommand;
use Src\Company\Project\Application\UseCases\Commands\StoreNotificationCommand;
use Src\Company\Document\Application\UseCases\Commands\StorePurchaseOrderItemCommand;
use Src\Company\Document\Application\UseCases\Commands\UpdatePurchaseOrderCommand;
use Src\Company\Document\Application\UseCases\Commands\UpdatePOWithItemsCommand;
use Src\Company\Document\Application\UseCases\Commands\DeletePurchaseOrderCommand;
use Src\Company\Document\Application\UseCases\Commands\DeletePurchaseOrderItemCommand;
use Src\Company\Document\Application\UseCases\Commands\ManagerSignPurchaseOrderCommand;
use Src\Company\Document\Application\UseCases\Commands\SendEmailsCommand;
use Src\Company\Document\Application\UseCases\Queries\FindAllPoItemQuery;
use Src\Company\Document\Application\UseCases\Queries\FindPurchaseOrderByProjectIdQuery;
use Src\Company\Document\Application\UseCases\Queries\FindPurchaseOrderByStatusOrderQuery;
use Src\Company\Document\Application\UseCases\Queries\FindAllPurchaseOrderQuery;
use Src\Company\Document\Application\UseCases\Queries\FindAllManagerEmailsQuery;
use Src\Company\Document\Application\UseCases\Queries\FindPurchaseOrderByIdQuery;
use Src\Company\Document\Application\UseCases\Queries\FindPurchaseOrderByStatusQuery;
use Src\Company\Document\Application\UseCases\Queries\GetPurchaseOrderNumberCount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Src\Company\Document\Application\UseCases\Queries\GetCompanyStampByProjectId;
use Src\Company\Document\Application\UseCases\Queries\GetPurchaseOrderDocumentTextQuery;
use Src\Company\Project\Application\Mappers\SupplierCostingMapper;
use Src\Company\Project\Domain\Model\Entities\SupplierCosting;
use Src\Company\Document\Application\Policies\PurchaseOrderPolicy;
use Src\Company\Document\Application\UseCases\Queries\FindQuotationItemsForPOQuery;
use Src\Company\Document\Infrastructure\EloquentModels\PurchaseOrderEloquentModel;
use Src\Company\Project\Application\UseCases\Queries\FindProjectByIdQuery;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        abort_if(authorize('view', PurchaseOrderPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Purchase Order!');

        try {

            $filters = $request->all();

            $purchaseOrders = (new FindAllPurchaseOrderQuery($filters))->handle();

            return response()->success($purchaseOrders, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function poListByProjectId($projectId): JsonResponse
    {
        abort_if(authorize('view', PurchaseOrderPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Purchase Order!');

        try {

            $pos = (new FindPurchaseOrderByProjectIdQuery($projectId, false))->handle();

            return response()->success($pos, 'success', Response::HTTP_OK);
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

            $pos = (new FindPurchaseOrderByProjectIdQuery($projectId, true))->handle();

            return response()->success($pos, 'success', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function purchaseOrderByStatus($status)
    {
        abort_if(authorize('view', PurchaseOrderPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Purchase Order!');

        try {

            $pos = (new FindPurchaseOrderByStatusQuery($status))->handle();

            return response()->success($pos, 'success', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function purchaseOrderList()
    {
        abort_if(authorize('view', PurchaseOrderPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Purchase Order!');

        try {

            $pos = (new FindPurchaseOrderByStatusOrderQuery())->handle();

            return response()->success($pos, 'Purchase order list ordered by pending,approved', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function poShow($id): JsonResponse
    {
        abort_if(authorize('view', PurchaseOrderPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Purchase Order!');

        try {

            $po = (new FindPurchaseOrderByIdQuery($id))->handle();

            return response()->success($po, 'success', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }



    public function poItemList($id): JsonResponse
    {
        abort_if(authorize('view', PurchaseOrderPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Purchase Order!');

        try {

            $items = (new FindAllPoItemQuery($id))->handle();

            return response()->success($items, 'success', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function getCompanyStamp($projectId): JsonResponse
    {
        try {

            $stamp = (new GetCompanyStampByProjectId($projectId))->handle();

            return response()->success($stamp, 'success', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function purchaseOrderCount()
    {
        abort_if(authorize('view', PurchaseOrderPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Purchase Order!');

        try {

            $purchaseOrderCount = (new GetPurchaseOrderNumberCount())->handle();

            return response()->success($purchaseOrderCount, 'success', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function store(StorePurchaseOrderRequest $request)
    {
        abort_if(authorize('store', PurchaseOrderPolicy::class), Response::HTTP_FORBIDDEN, 'Need store permission for Purchase Order!');

        try {

            $po = PurchaseOrderMapper::fromRequest($request);

            $poData = (new StorePurchaseOrderCommand($po))->execute();

            $poId = $poData->id;

            $itemRequests = $request->items;

            $poItems = PurchaseOrderItemMapper::fromRequest(json_decode($itemRequests));

            (new StorePurchaseOrderItemCommand($poItems, $poId))->execute();

            // $message = "Purchase order from salesperson have successfully sent to management";

            // (new StoreNotificationCommand($message))->execute();  // store notification

            // $emails = (new FindAllManagerEmailsQuery())->handle();  //find managers emails first

            // (new SendEmailsCommand($emails,$poData))->execute();  //send email to all manager mails
            $request['po_id'] = $poId;
            $this->downloadPdf($request);
            return response()->success($itemRequests, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function update(int $id, UpdatePurchaseOrderRequest $request): JsonResponse
    {
        abort_if(authorize('update', PurchaseOrderPolicy::class), Response::HTTP_FORBIDDEN, 'Need update permission for Purchase Order!');

        try {

            $po = (new UpdatePOWithItemsCommand($request, $id))->execute();
            $request['po_id'] = $id;
            $this->downloadPdf($request);
            return response()->success($po, 'success', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function updateForSaleReport(int $id, UpdatePurchaseOrderForSaleReportRequest $request): JsonResponse
    {
        abort_if(authorize('update', PurchaseOrderPolicy::class), Response::HTTP_FORBIDDEN, 'Need update permission for Purchase Order!');

        try {

            $po = (new UpdatePurchaseOrderCommand($request, $id))->execute();

            return response()->success($po, 'success', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function managerSign(HttpRequest $request)
    {
        abort_if(authorize('sign', PurchaseOrderPolicy::class), Response::HTTP_FORBIDDEN, 'Need sign permission for Purchase Order!');

        try {

            (new ManagerSignPurchaseOrderCommand($request))->execute();
            $request['po_id'] = $request->id;
            $this->downloadPdf($request);

            return response()->success(null, 'success', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        abort_if(authorize('destroy', PurchaseOrderPolicy::class), Response::HTTP_FORBIDDEN, 'Need destroy permission for Purchase Order!');

        try {
            (new DeletePurchaseOrderCommand($id))->execute();

            return response()->success($id, "Successfully Deleted", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function destroyItem(int $id): JsonResponse
    {
        abort_if(authorize('destroy_item', PurchaseOrderPolicy::class), Response::HTTP_FORBIDDEN, 'Need destroy_item permission for Purchase Order!');

        try {
            (new DeletePurchaseOrderItemCommand($id))->execute();

            return response()->success($id, "Successfully Deleted", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
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
    public function downloadPdf(Request $request)
    {
        $po_id = $request->po_id;
        $projectId = $request->project_id;
        $folder_name  = env('COMPANY_FOLDER_NAME', 'Twp');
        $project = (new FindProjectByIdQuery($projectId))->handle();
        $poData = (new FindPurchaseOrderByIdQuery($po_id))->handle();
        $poCollectData = collect($poData['po']);
        $poFooterCollectData = collect($poData['poFooter']);
        $stamp = (new GetCompanyStampByProjectId($projectId))->handle();

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

    public function getQuotationItemForPO($projectId)
    {
        try {

            $quotationItems = (new FindQuotationItemsForPOQuery($projectId))->handle();

            return response()->success($quotationItems, 'success', Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);

        }
    }
}
