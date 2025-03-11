<?php

namespace Src\Company\Project\Presentation\API;

use Src\Common\Infrastructure\Laravel\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Src\Company\Project\Application\Mappers\SupplierDebitMapper;
use Src\Company\Project\Application\Policies\SaleReportPolicy;
use Src\Company\Project\Application\Requests\StoreSupplierDebitRequest;
use Src\Company\Project\Application\UseCases\Commands\StoreSupplierDebitCommand;
use Src\Company\Project\Application\UseCases\Commands\UpdateSupplierDebitCommand;
use Src\Company\Project\Application\UseCases\Queries\FindSupplierDebitByIdQuery;
use Src\Company\Project\Application\UseCases\Queries\FindSupplierDebitBySaleReportIdQuery;
use Src\Company\Project\Application\UseCases\Queries\GetAllSupplierDebitQuery;
use Src\Company\Project\Application\UseCases\Queries\GetSupplierDebitReportQuery;

class SupplierDebitController extends Controller
{
    public function index(Request $request)
    {
        //abort_if(authorize('view_pending_supplier_costing', SaleReportPolicy::class), Response::HTTP_FORBIDDEN, 'Need view_pending_supplier_costing permission for Supplier Costing!');

        try {

            $filters = $request->all();

            $supplierCostingPayment = (new GetAllSupplierDebitQuery($filters))->handle();

            return response()->success($supplierCostingPayment, 'success', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function getBySaleReportId($saleReportId)
    {
        //abort_if(authorize('view_pending_supplier_costing', SaleReportPolicy::class), Response::HTTP_FORBIDDEN, 'Need view_pending_supplier_costing permission for Supplier Costing!');

        try {

            $supplierCostingPayment = (new FindSupplierDebitBySaleReportIdQuery($saleReportId))->handle();

            return response()->success($supplierCostingPayment, 'success', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function getReport(Request $request)
    {
        //abort_if(authorize('view_supplier_costing', SaleReportPolicy::class), Response::HTTP_FORBIDDEN, 'Need view_supplier_costing permission for Supplier Costing Report!');

        try {

            $filters = $request->all();

            $supplierDebitReport = (new GetSupplierDebitReportQuery($filters))->handle();

            return response()->success($supplierDebitReport, 'success', Response::HTTP_OK);

        } catch (UnauthorizedUserException $e) {

            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function show(int $id)
    {
        //abort_if(authorize('view_supplier_costing', SaleReportPolicy::class), Response::HTTP_FORBIDDEN, 'Need view_pending_supplier_costing permission for Supplier Costing');

        try {

            $supplierCostingPayment = (new FindSupplierDebitByIdQuery($id))->handle();

            return response()->success($supplierCostingPayment, 'success', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function store(StoreSupplierDebitRequest $request)
    {
        //abort_if(authorize('store_supplier_credit', SaleReportPolicy::class), Response::HTTP_FORBIDDEN, 'Need store_supplier_credit permission for Sale Report!');

        try {

            $supplierCredit = SupplierDebitMapper::fromRequest($request);

            $supplierCreditData = (new StoreSupplierDebitCommand($supplierCredit))->execute();

            return response()->success($supplierCreditData, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function update(StoreSupplierDebitRequest $request,int $id)
    {
        //abort_if(authorize('update_supplier_credit', SaleReportPolicy::class), Response::HTTP_FORBIDDEN, 'Need store_supplier_credit permission for Sale Report!');

        try {

            $supplierCredit = SupplierDebitMapper::fromRequest($request,$id);

            $supplierCreditData = (new UpdateSupplierDebitCommand($supplierCredit))->execute();

            return response()->success($supplierCreditData, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
