<?php

namespace Src\Company\Project\Presentation\API;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Src\Common\Infrastructure\Laravel\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Src\Company\Document\Domain\Imports\VendorInvoicesImport;
use Src\Company\Project\Application\Policies\SaleReportPolicy;
use Src\Company\Project\Application\UseCases\Queries\GetAllSupplierCostingQuery;
use Src\Company\Project\Application\UseCases\Queries\GetSupplierCostingByIdQuery;
use Src\Company\Project\Application\UseCases\Commands\VerifySupplierCostingCommand;
use Src\Company\Project\Application\UseCases\Queries\GetSupplierCostingReportQuery;
use Src\Company\Project\Application\UseCases\Commands\ApproveSupplierCostingCommand;
use Src\Company\Project\Application\UseCases\Queries\GetSupplierCostingWithVendorAndProjectQuery;

class SupplierCostingController extends Controller
{
    public function index(Request $request):JsonResponse
    {
        try {

            $filters = $request->all();

            $data = (new GetAllSupplierCostingQuery($filters))->handle();

            return response()->success($data,'success',Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }    
    }

    public function getByVendorAndProject(Request $request): JsonResponse
    {
        abort_if(authorize('view_supplier_costing', SaleReportPolicy::class), Response::HTTP_FORBIDDEN, 'Need view_supplier_costing permission for Vendor Invoice!');

        try {

            $projectId = $request->project_id;
            $vendorId = $request->vendor_id;

            $data = (new GetSupplierCostingWithVendorAndProjectQuery($vendorId,$projectId))->handle();

            return response()->success($data,'success',Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function show($id): JsonResponse
    {
        try {

            $data = (new GetSupplierCostingByIdQuery($id))->handle();

            return response()->success($data,'success',Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function verify($id): JsonResponse
    {   
        abort_if(authorize('verify_supplier_costing', SaleReportPolicy::class), Response::HTTP_FORBIDDEN, 'Need verify_supplier_costing permission for Supplier Costing Verify From SalePerson!');

        try {

            $verifyBy =  auth('sanctum')->user()->id;

            $data = (new VerifySupplierCostingCommand($id,$verifyBy))->execute();

            return response()->success($data,'success',Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }

    public function approve($id): JsonResponse
    {   
        abort_if(authorize('sign_supplier_costing_manager', SaleReportPolicy::class), Response::HTTP_FORBIDDEN, 'Need sign_supplier_costing_manager permission for Supplier Costing Approve From Management!');

        try {

            $data = (new ApproveSupplierCostingCommand($id))->execute();

            return response()->success($data,'success',Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }

    public function getReport(Request $request)
    {
        abort_if(authorize('view_supplier_costing', SaleReportPolicy::class), Response::HTTP_FORBIDDEN, 'Need view_supplier_costing permission for Supplier Costing Report!');

        try {

            $filters = $request->all();

            $supplierCostingReport = (new GetSupplierCostingReportQuery($filters))->handle();

            return response()->success($supplierCostingReport, 'success', Response::HTTP_OK);

        } catch (UnauthorizedUserException $e) {

            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function import(Request $request)
    {
        try {

            $uploadFile = $request->file('vendor_invoice_excel');
           Log::info('uploadFile',[$uploadFile]);
            $filePath = $uploadFile->getRealPath();

            $spreadsheet = IOFactory::load($filePath);

            $sheetNames = $spreadsheet->getSheetNames();

            $sheetsData = Excel::toArray([], $uploadFile);

            foreach ($sheetsData as $index => $sheet) {
                $sheetName = $sheetNames[$index] ?? "Sheet " . ($index + 1);
                $import = new VendorInvoicesImport($sheetName);
                $import->collection(collect($sheet));
            }
            return response()->success(null, "Successfully Excel Imported", Response::HTTP_OK);

        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }
}
