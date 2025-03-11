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
use Src\Company\Project\Application\UseCases\Queries\GetAllSupplierCostingMobileQuery;
use Src\Company\Project\Application\UseCases\Queries\GetSupplierCostingByIdQuery;
use Src\Company\Project\Application\UseCases\Commands\VerifySupplierCostingCommand;
use Src\Company\Project\Application\UseCases\Queries\GetSupplierCostingReportQuery;
use Src\Company\Project\Application\UseCases\Commands\ApproveSupplierCostingCommand;
use Src\Company\Project\Application\UseCases\Queries\GetSupplierCostingWithVendorAndProjectQuery;

class SupplierCostingMobileController extends Controller
{

    public function index(Request $request): JsonResponse
    {
        try {

            $filters = $request->all();

            $data = (new GetAllSupplierCostingMobileQuery($filters))->handle();

            return response()->success($data,'success',Response::HTTP_OK);


        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function show($id): JsonResponse
    {
        try {

            $data = (new GetSupplierCostingByIdQuery($id))->handle();

            return response()->success($data,'success',Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function verify ($id)
    {
        try {

            $verifyBy =  auth('sanctum')->user()->id;

            $data = (new VerifySupplierCostingCommand($id,$verifyBy))->execute();

            return response()->success($data,'Supplier Costing Verify Successful !',Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function approve($id): JsonResponse
    {
        abort_if(authorize('sign_supplier_costing_manager', SaleReportPolicy::class), Response::HTTP_FORBIDDEN, 'Need sign_supplier_costing_manager permission for Supplier Costing Approve From Management!');

        try {

            $data = (new ApproveSupplierCostingCommand($id))->execute();

            return response()->success($data,'Supplier Costing Approved Successful ',Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }

}
