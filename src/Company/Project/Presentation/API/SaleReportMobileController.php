<?php

namespace Src\Company\Project\Presentation\API;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Src\Company\Project\Application\Mappers\SaleReportMapper;
use Src\Company\Project\Application\Policies\SaleReportPolicy;
use Src\Company\Project\Application\Requests\UpdateSaleReportRequest;
use Src\Company\Project\Application\UseCases\Queries\FindCustomerPaymentMobileQuery;
use Src\Company\Project\Application\UseCases\Queries\FindSalepersonKpiReportMonthMobileQuery;
use Src\Company\Project\Application\UseCases\Queries\FindSaleReportByProjectIdMobileQuery;
use Src\Company\Project\Application\UseCases\Queries\FindSupplierCostingByProjectIdMobileQuery;
use Src\Company\Project\Application\UseCases\Queries\FindSupplierCreditBySaleReportIdMobileQuery;
use UpdateSaleReportMobileCommand;

class SaleReportMobileController extends Controller
{

    public function salepersonKpiReportMonth(Request $request, $salespersonUserId)
    {
        abort_if(authorize('view_salesperson_kpi_report', SaleReportPolicy::class), Response::HTTP_FORBIDDEN, 'Need view_salesperson_kpi_report permission for Sale Report!');

        // saleperson_id, year, month
        try {
            $year = $request->input('year') ? (int)$request->year : null;
            $month = $request->input('month') ? (int)$request->month : null;

            $saleReportData = (new FindSalepersonKpiReportMonthMobileQuery($salespersonUserId, $month, $year))->handle();

            return response()->success($saleReportData, 'success', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function getCustomerPayment($saleReportId)
    {
        abort_if(authorize('view_customer_payment', SaleReportPolicy::class), Response::HTTP_FORBIDDEN, 'Need view_customer_payment permission for Sale Report!');

        try {

            $data = (new FindCustomerPaymentMobileQuery($saleReportId))->handle();

            return response()->success($data, 'success', Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function getSupplierCostingsByProjectId($projectId): JsonResponse
    {
        abort_if(authorize('view_supplier_costing', SaleReportPolicy::class), Response::HTTP_FORBIDDEN, 'Need view_supplier_costing permission for Sale Report!');

        try {

            $supplierCostings = (new FindSupplierCostingByProjectIdMobileQuery($projectId))->handle();

            return response()->success($supplierCostings, 'success', Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function getSupplierCredit($saleReportId)
    {
        //abort_if(authorize('view_supplier_credit', SaleReportPolicy::class), Response::HTTP_FORBIDDEN, 'Need view_supplier_credit permission for Sale Report!');

        try {

            $data = (new FindSupplierCreditBySaleReportIdMobileQuery($saleReportId))->handle();

            return response()->success($data, 'success', Response::HTTP_OK);

        } catch (UnauthorizedUserException $e) {

            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function getSaleReportByProjectId($projectId)
    {
        abort_if(authorize('view_sale_report', SaleReportPolicy::class), Response::HTTP_FORBIDDEN, 'Need view_sale_report permission for Sale Report!');

        try {

            $data = (new FindSaleReportByProjectIdMobileQuery($projectId))->handle();

            return response()->success($data, 'success', Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function updateSaleReport(UpdateSaleReportRequest $request, int $id): JsonResponse
    {
        abort_if(authorize('update_sale_report', SaleReportPolicy::class), Response::HTTP_FORBIDDEN, 'Need update_sale_report permission for Sale Report!');

        try {
            $saleReport = SaleReportMapper::fromRequest($request, $id);
            $saleCommissions = $request->sale_commissions;

            $saleReportData = (new UpdateSaleReportMobileCommand($saleReport, $saleCommissions))->execute();

            return response()->success($saleReportData, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }
}
