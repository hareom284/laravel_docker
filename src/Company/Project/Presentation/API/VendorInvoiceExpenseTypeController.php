<?php

namespace Src\Company\Project\Presentation\API;

use Aimeos\GraphQL\Type\Definition\Json;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\Project\Application\Mappers\VendorInvoiceExpenseTypeMapper;
use Src\Company\Project\Application\Requests\StoreVendorInvoiceExpenseTypeRequest;
use Src\Company\Project\Application\UseCases\Commands\DeleteVendorInvoiceExpenseTypeCommand;
use Src\Company\Project\Application\UseCases\Commands\StoreVendorInvoiceExpenseTypeCommand;
use Src\Company\Project\Application\UseCases\Commands\UpdateVendorInvoiceExpenseTypeCommand;
use Src\Company\Project\Application\UseCases\Queries\GetAllVendorExpenseTypesWithoutPaginationQuery;
use Src\Company\Project\Application\UseCases\Queries\GetAllVendorExpenseTypesWithPaginationQuery;
use Src\Company\Project\Application\UseCases\Queries\GetVendorExpenseTypeWithIdQuery;

class VendorInvoiceExpenseTypeController extends Controller
{
    public function index(Request $request)
    {
        //abort_if(authorize('view_pending_supplier_costing', SaleReportPolicy::class), Response::HTTP_FORBIDDEN, 'Need view_pending_supplier_costing permission for Supplier Costing!');

        try {

            $filters = $request->all();

            $expenseTypes = (new GetAllVendorExpenseTypesWithPaginationQuery($filters))->handle();

            return response()->success($expenseTypes, 'success', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function all()
    {
        //abort_if(authorize('view_pending_supplier_costing', SaleReportPolicy::class), Response::HTTP_FORBIDDEN, 'Need view_pending_supplier_costing permission for Supplier Costing!');

        try {

            $expenseTypes = (new GetAllVendorExpenseTypesWithoutPaginationQuery())->handle();

            return response()->success($expenseTypes, 'success', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function show($id) :JsonResponse
    {
        //abort_if(authorize('view_supplier_credit', SaleReportPolicy::class), Response::HTTP_FORBIDDEN, 'Need view_supplier_credit permission for Sale Report!');

        try {

            $expenseType = (new GetVendorExpenseTypeWithIdQuery($id))->handle();

            return response()->success($expenseType, 'success', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function store(StoreVendorInvoiceExpenseTypeRequest $request)
    {
        //abort_if(authorize('store_supplier_credit', SaleReportPolicy::class), Response::HTTP_FORBIDDEN, 'Need store_supplier_credit permission for Sale Report!');

        try {

            $expenseType = VendorInvoiceExpenseTypeMapper::fromRequest($request);

            $expenseTypeData = (new StoreVendorInvoiceExpenseTypeCommand($expenseType))->execute();

            return response()->success($expenseTypeData, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function update(StoreVendorInvoiceExpenseTypeRequest $request,int $id)
    {
        //abort_if(authorize('update_supplier_credit', SaleReportPolicy::class), Response::HTTP_FORBIDDEN, 'Need store_supplier_credit permission for Sale Report!');

        try {

            $expenseType = VendorInvoiceExpenseTypeMapper::fromRequest($request,$id);

            $expenseTypeData = (new UpdateVendorInvoiceExpenseTypeCommand($expenseType))->execute();

            return response()->success($expenseTypeData, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        //check if user's has permission

        try {

            (new DeleteVendorInvoiceExpenseTypeCommand($id))->execute();

            return response()->success(null, "Deleted Successfully", Response::HTTP_OK);

        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }
}