<?php

namespace Src\Company\Project\Presentation\API;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Src\Common\Infrastructure\Laravel\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Src\Company\Project\Application\Mappers\SaleReportMapper;
use Src\Company\Project\Application\Policies\SaleReportPolicy;
use Src\Company\Project\Application\Mappers\AdvancePaymentMapper;
use Src\Company\Project\Application\Mappers\SupplierCreditMapper;
use Src\Company\Project\Application\Mappers\CustomerPaymentMapper;
use Src\Company\Project\Application\Mappers\SupplierCostingMapper;
use Src\Company\Project\Application\Requests\AdvancePaymentRequest;
use Src\Company\Project\Application\Requests\UpdateSaleReportRequest;
use Src\Company\Project\Application\Requests\StoreSupplierCreditRequest;
use Src\Company\Document\Application\Requests\StoreJobSheetUploadRequest;
use Src\Company\Project\Application\Requests\StoreCustomerPaymentRequest;
use Src\Company\Project\Application\Requests\StoreSupplierCostingRequest;
use Src\Company\Project\Application\Requests\RefundCustomerPaymentRequest;
use Src\Company\Project\Application\Requests\UpdateCustomerPaymentRequest;
use Src\Company\Project\Application\Requests\UpdateSupplierCostingRequest;
use Src\Company\Project\Application\UseCases\Commands\UpdateSaleReportCommand;
use Src\Company\Project\Application\UseCases\Queries\FindCustomerPaymentQuery;
use Src\Company\Project\Infrastructure\EloquentModels\SaleReportEloquentModel;
use Src\Company\Project\Application\UseCases\Queries\GetAllAdvancePaymentQuery;
use Src\Company\Project\Application\UseCases\Queries\GetAllSupplierCreditQuery;
use Src\Company\Project\Application\UseCases\Queries\GetAllCustomerPaymentQuery;
use Src\Company\Project\Application\UseCases\Commands\StoreAdvancePaymentCommand;
use Src\Company\Project\Application\UseCases\Commands\StoreSupplierCreditCommand;
use Src\Company\Project\Application\UseCases\Queries\FindSupplierCreditByIdQuery;
use Src\Company\Project\Application\UseCases\Queries\GetAllSaleReportByYearQuery;
use Src\Company\Project\Application\UseCases\Commands\StoreCustomerPaymentCommand;
use Src\Company\Project\Application\UseCases\Commands\StoreSupplierCostingCommand;
use Src\Company\Project\Application\UseCases\Commands\UpdateAdvancePaymentCommand;
use Src\Company\Project\Application\UseCases\Commands\UpdateSupplierCreditCommand;
use Src\Company\Project\Application\UseCases\Queries\GetAllSaleReportByMonthQuery;
use Src\Company\Project\Application\UseCases\Queries\GetSupplierCreditReportQuery;
use Src\Company\Project\Application\UseCases\Commands\DeleteCustomerPaymentCommand;
use Src\Company\Project\Application\UseCases\Commands\DeleteSupplierCostingCommand;
use Src\Company\Project\Application\UseCases\Commands\RefundCustomerPaymentCommand;
use Src\Company\Project\Application\UseCases\Commands\SignSaleReportCommand;
use Src\Company\Project\Application\UseCases\Commands\UpdateCustomerPaymentCommand;
use Src\Company\Project\Application\UseCases\Commands\UpdateSupplierCostingCommand;
use Src\Company\Project\Application\UseCases\Queries\GetInvoicesFromQuickbookQuery;
use Src\Company\Project\Application\UseCases\Queries\FindSaleReportByProjectIdQuery;
use Src\Company\Project\Application\UseCases\Queries\GetSalespersonReportByYearQuery;
use Src\Company\Project\Application\UseCases\Queries\FindSalepersonKpiReportYearQuery;
use Src\Company\Project\Application\UseCases\Queries\GetSalespersonReportByMonthQuery;
use Src\Company\Project\Application\UseCases\Queries\FindSalepersonKpiReportMonthQuery;
use Src\Company\Project\Application\UseCases\Commands\StoreCustomerPaymentWithQboCommand;
use Src\Company\Project\Application\UseCases\Commands\StoreSupplierCostingWithQboCommand;
use Src\Company\Project\Application\UseCases\Commands\UpdateMarkedSaleReportCommand;
use Src\Company\Project\Application\UseCases\Queries\FindSupplierCostingByProjectIdQuery;
use Src\Company\Project\Application\UseCases\Queries\FindAdvancePaymentBySaleReportIdQuery;
use Src\Company\Project\Application\UseCases\Queries\FindSupplierCreditBySaleReportIdQuery;
use Src\Company\Project\Application\UseCases\Queries\FindCompanySaleReportWithKpiInYearQuery;
use Src\Company\Project\Application\UseCases\Queries\FindSalepersonSaleReportWithKpiInYearQuery;
use Src\Company\Project\Application\UseCases\Queries\GetManagerPendingApproveDocumentQuery;
use Src\Company\Project\Application\UseCases\Queries\GetPendingApproveDocumentQuery;

class SaleReportController extends Controller
{
    public function getAllCustomerPayment(Request $request): JsonResponse
    {
        abort_if(authorize('view_customer_payment', SaleReportPolicy::class), Response::HTTP_FORBIDDEN, 'Need view_customer_payment permission for Sale Report!');

        try {

            $filters = $request->all();

            $data = (new GetAllCustomerPaymentQuery($filters))->handle();

            return response()->success($data,'success',Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        } 
    }

    public function getCustomerPayment($saleReportId)
    {
        abort_if(authorize('view_customer_payment', SaleReportPolicy::class), Response::HTTP_FORBIDDEN, 'Need view_customer_payment permission for Sale Report!');

        try {

            $data = (new FindCustomerPaymentQuery($saleReportId))->handle();

            return response()->success($data, 'success', Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function getSupplierCostingsByProjectId($projectId): JsonResponse
    {
        abort_if(authorize('view_supplier_costing', SaleReportPolicy::class), Response::HTTP_FORBIDDEN, 'Need view_supplier_costing permission for Sale Report!');

        try {

            $supplierCostings = (new FindSupplierCostingByProjectIdQuery($projectId))->handle();

            return response()->success($supplierCostings, 'success', Response::HTTP_OK);
            
        } catch (UnauthorizedUserException $e) {

            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function getSaleReportByProjectId($projectId)
    {
        abort_if(authorize('view_sale_report', SaleReportPolicy::class), Response::HTTP_FORBIDDEN, 'Need view_sale_report permission for Sale Report!');

        try {

            $data = (new FindSaleReportByProjectIdQuery((int) $projectId))->handle();

            return response()->success($data, 'success', Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function storeCustomerPayment(StoreCustomerPaymentRequest $request): JsonResponse
    {
        abort_if(authorize('store_customer_payment', SaleReportPolicy::class), Response::HTTP_FORBIDDEN, 'Need store_customer_payment permission for Sale Report!');

        try {
            $customerPayment = CustomerPaymentMapper::fromRequest($request);

            $customerPaymentData = (new StoreCustomerPaymentCommand($customerPayment))->execute();

            if (!$customerPaymentData) {

                return response()->error(null, "Customer Payment is exceeded Project's Remaining Amount.", Response::HTTP_NOT_ACCEPTABLE);
            } else {

                return response()->success($customerPaymentData, 'success', Response::HTTP_CREATED);
            }
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function storeCustomerPaymentWithQbo(Request $request): JsonResponse
    {
        abort_if(authorize('store_customer_payment', SaleReportPolicy::class), Response::HTTP_FORBIDDEN, 'Need store_customer_payment permission for Sale Report!');

        try {

            $customerPaymentData = (new StoreCustomerPaymentWithQboCommand($request->projectId))->execute();
            
            return response()->success($customerPaymentData, 'success', Response::HTTP_OK);
            
        }catch (Exception $e) {

            return response()->error(null, $e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);

        }catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function updateCustomerPayment(UpdateCustomerPaymentRequest $request, int $id): JsonResponse
    {
        abort_if(authorize('update_customer_payment', SaleReportPolicy::class), Response::HTTP_FORBIDDEN, 'Need update_customer_payment permission for Sale Report!');

        try {
            $customerPayment = CustomerPaymentMapper::fromRequest($request, $id);

            $customerPaymentData = (new UpdateCustomerPaymentCommand($customerPayment))->execute();

            if (!$customerPaymentData['status']) {

                return response()->error(null, $customerPaymentData['data'], Response::HTTP_NOT_ACCEPTABLE);
            } else {

                return response()->success($customerPaymentData['data'], 'success', Response::HTTP_CREATED);
            }
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function refundCustomerPayment(RefundCustomerPaymentRequest $request,int $id): JsonResponse
    {
        //abort_if(authorize('refund_customer_payment', SaleReportPolicy::class), Response::HTTP_FORBIDDEN, 'Need refund_customer_payment permission for Sale Report!');

        try {

            $data = $request->only(['amount', 'remark', 'refund_date']);

            $result = (new RefundCustomerPaymentCommand($data,$id))->execute();

            if($result['status'] == false) {
                return response()->error("Cannot Refund This Payment.",$result['data'],Response::HTTP_UNPROCESSABLE_ENTITY);
            }else {
                return response()->success($result['data'], "Sucessfully refunded", Response::HTTP_OK);
            }

        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }catch (ModelNotFoundException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function destroyCustomerPayment(int $id): JsonResponse
    {
        //check if user's has permission
        abort_if(authorize('destroy_customer_payment', SaleReportPolicy::class), Response::HTTP_FORBIDDEN, 'Need destroy_customer_payment permission for Sale Report!');

        try {
            (new DeleteCustomerPaymentCommand($id))->execute();

            return response()->success($id, "Sucessfully deleted", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function storeSupplierCosting(StoreSupplierCostingRequest $request): JsonResponse
    {
        abort_if(authorize('store_supplier_costing', SaleReportPolicy::class), Response::HTTP_FORBIDDEN, 'Need store_supplier_costing permission for Sale Report!');

        try {

            $supplierCosting = SupplierCostingMapper::fromRequest($request);

            $data = (new StoreSupplierCostingCommand($supplierCosting))->execute();

            return response()->success($data, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function storeSupplierCostingWithQbo(Request $request): JsonResponse
    {
        abort_if(authorize('store_supplier_costing', SaleReportPolicy::class), Response::HTTP_FORBIDDEN, 'Need store_supplier_costing permission for Sale Report!');

        try {

            $customerPaymentData = (new StoreSupplierCostingWithQboCommand($request->projectId))->execute();
            
            return response()->success($customerPaymentData, 'success', Response::HTTP_CREATED);
            
        }catch (Exception $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
            
        }catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function updateSupplierCosting(UpdateSupplierCostingRequest $request, int $id): JsonResponse
    {
        abort_if(authorize('update_supplier_costing', SaleReportPolicy::class), Response::HTTP_FORBIDDEN, 'Need update_supplier_costing permission for Sale Report!');

        try {
            $supplierCosting = SupplierCostingMapper::fromRequest($request, $id);

            $supplierCostingData = (new UpdateSupplierCostingCommand($supplierCosting))->execute();

            return response()->success($supplierCostingData, 'successfully updated', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function destroySupplierCosting(int $supplier_costing_id): JsonResponse
    {
        abort_if(authorize('destroy_supplier_costing', SaleReportPolicy::class), Response::HTTP_FORBIDDEN, 'Need destroy_supplier_costing permission for Sale Report!');

        try {
            (new DeleteSupplierCostingCommand($supplier_costing_id))->execute();

            return response()->success($supplier_costing_id, "Sucessfully deleted", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function getAllAdvancePayments(Request $request): JsonResponse
    {
        //abort_if(authorize('view_advance_payment', SaleReportPolicy::class), Response::HTTP_FORBIDDEN, 'Need view_advance_payment permission for Sale Report!');

        try {

            $filters = $request->all();

            $advancePaymentData = (new GetAllAdvancePaymentQuery($filters))->handle();

            return response()->success($advancePaymentData, 'success', Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function getAdvancePaymentBySaleReportId(int $saleReportId): JsonResponse
    {
        //abort_if(authorize('view_advance_payment', SaleReportPolicy::class), Response::HTTP_FORBIDDEN, 'Need view_advance_payment permission for Sale Report!');

        try {

            $advancePaymentData = (new FindAdvancePaymentBySaleReportIdQuery($saleReportId))->handle();

            return response()->success($advancePaymentData, 'success', Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function storeAdvancePayment(AdvancePaymentRequest $request): JsonResponse
    {
        //abort_if(authorize('store_advance_payment', SaleReportPolicy::class), Response::HTTP_FORBIDDEN, 'Need store_advance_payment permission for Sale Report!');
        try {

            $advancePayment = AdvancePaymentMapper::fromRequest($request);

            $advancePaymentData = (new StoreAdvancePaymentCommand($advancePayment))->execute();

            return response()->success($advancePaymentData, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function updateAdvancePayment(AdvancePaymentRequest $request, int $id): JsonResponse
    {
        //abort_if(authorize('update_advance_payment', SaleReportPolicy::class), Response::HTTP_FORBIDDEN, 'Need update_advance_payment permission for Sale Report!');

        try {

            $advancePayment = AdvancePaymentMapper::fromRequest($request, $id);

            $advancePaymentData = (new UpdateAdvancePaymentCommand($advancePayment))->execute();

            return response()->success($advancePaymentData, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function getAllSupplierCredits(Request $request): JsonResponse
    {
        try {

            $filters = $request->all();

            $data = (new GetAllSupplierCreditQuery($filters))->handle();

            return response()->success($data,'success',Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        } 
    }

    public function getSupplierCreditDetails($id):JsonResponse
    {
        try {

            $data = (new FindSupplierCreditByIdQuery($id))->handle();

            return response()->success($data, 'success', Response::HTTP_OK);

        } catch (UnauthorizedUserException $e) {

            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function getSupplierCredit($saleReportId)
    {
        //abort_if(authorize('view_supplier_credit', SaleReportPolicy::class), Response::HTTP_FORBIDDEN, 'Need view_supplier_credit permission for Sale Report!');

        try {

            $data = (new FindSupplierCreditBySaleReportIdQuery($saleReportId))->handle();

            return response()->success($data, 'success', Response::HTTP_OK);

        } catch (UnauthorizedUserException $e) {

            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function getSupplierCreditReport(Request $request)
    {
        //abort_if(authorize('view_supplier_costing', SaleReportPolicy::class), Response::HTTP_FORBIDDEN, 'Need view_supplier_costing permission for Supplier Costing Report!');

        try {

            $filters = $request->all();

            $supplierCreditReport = (new GetSupplierCreditReportQuery($filters))->handle();

            return response()->success($supplierCreditReport, 'success', Response::HTTP_OK);

        } catch (UnauthorizedUserException $e) {

            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function storeSupplierCredit(StoreSupplierCreditRequest $request)
    {
        //abort_if(authorize('store_supplier_credit', SaleReportPolicy::class), Response::HTTP_FORBIDDEN, 'Need store_supplier_credit permission for Sale Report!');

        try {

            $supplierCredit = SupplierCreditMapper::fromRequest($request);

            $supplierCreditData = (new StoreSupplierCreditCommand($supplierCredit))->execute();

            return response()->success($supplierCreditData, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function updateSupplierCredit(StoreSupplierCreditRequest $request,int $id)
    {
        //abort_if(authorize('store_supplier_credit', SaleReportPolicy::class), Response::HTTP_FORBIDDEN, 'Need store_supplier_credit permission for Sale Report!');

        try {

            $supplierCredit = SupplierCreditMapper::fromRequest($request,$id);

            $supplierCreditData = (new UpdateSupplierCreditCommand($supplierCredit))->execute();

            return response()->success($supplierCreditData, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function updateSaleReport(UpdateSaleReportRequest $request, int $id): JsonResponse
    {
        abort_if(authorize('update_sale_report', SaleReportPolicy::class), Response::HTTP_FORBIDDEN, 'Need update_sale_report permission for Sale Report!');

        try {
            $saleReport = SaleReportMapper::fromRequest($request, $id);
            $saleCommissions = $request->sale_commissions;

            $saleReportData = (new UpdateSaleReportCommand($saleReport, $saleCommissions))->execute();

            return response()->success($saleReportData, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function getSalReportByYear(Request $request): JsonResponse
    {
        abort_if(authorize('view_sale_report', SaleReportPolicy::class), Response::HTTP_FORBIDDEN, 'Need view_sale_report permission for Sale Report!');

        try {

            $year = $request->year;
            $companyId = $request->companyId;
            $startDate = $request->startDate ? \DateTime::createFromFormat('j/n/Y', $request->startDate)->format('Y-m-d') : null;
            $endDate = $request->endDate ? \DateTime::createFromFormat('j/n/Y', $request->endDate)->format('Y-m-d') : null;

            $saleReportData = (new GetAllSaleReportByYearQuery($companyId, $year, $startDate, $endDate))->handle();

            return response()->success($saleReportData, 'success', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function getSalReportByMonth(Request $request): JsonResponse
    {
        abort_if(authorize('view_sale_report', SaleReportPolicy::class), Response::HTTP_FORBIDDEN, 'Need view_sale_report permission for Sale Report!');

        try {

            $year = $request->year;

            $month = $request->month;

            $companyId = $request->companyId;
            $startDate = $request->startDate ? \DateTime::createFromFormat('j/n/Y', $request->startDate)->format('Y-m-d') : null;
            $endDate = $request->endDate ? \DateTime::createFromFormat('j/n/Y', $request->endDate)->format('Y-m-d') : null;

            $saleReportData = (new GetAllSaleReportByMonthQuery($companyId, $year, $month, $startDate, $endDate))->handle();

            return response()->success($saleReportData, 'success', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function getSalespersonReportByYear(Request $request)
    {
        abort_if(authorize('view_salesperson_report', SaleReportPolicy::class), Response::HTTP_FORBIDDEN, 'Need view_salesperson_report permission for Sale Report!');

        try {

            $salespersonId = $request->salesperson_id;

            $year = $request->year;

            $saleReportData = (new GetSalespersonReportByYearQuery($salespersonId, $year))->handle();

            return response()->success($saleReportData, 'success', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function getSalespersonReportByMonth(Request $request)
    {
        abort_if(authorize('view_salesperson_report', SaleReportPolicy::class), Response::HTTP_FORBIDDEN, 'Need view_salesperson_report permission for Sale Report!');

        try {

            $salespersonId = $request->salesperson_id;

            $month = $request->month;

            $year = $request->year;

            $saleReportData = (new GetSalespersonReportByMonthQuery($salespersonId, $month, $year))->handle();

            return response()->success($saleReportData, 'success', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function salepersonKpiReportMonth(Request $request)
    {
        abort_if(authorize('view_salesperson_kpi_report', SaleReportPolicy::class), Response::HTTP_FORBIDDEN, 'Need view_salesperson_kpi_report permission for Sale Report!');

        // saleperson_id, year, month
        try {

            $saleReportData = (new FindSalepersonKpiReportMonthQuery($request->saleperson_id, $request->month, $request->year))->handle();

            return response()->success($saleReportData, 'success', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function salepersonKpiReportYear(Request $request)
    {
        abort_if(authorize('view_salesperson_kpi_report', SaleReportPolicy::class), Response::HTTP_FORBIDDEN, 'Need view_salesperson_kpi_report permission for Sale Report!');

        // saleperson_id, year
        try {

            $saleReportData = (new FindSalepersonKpiReportYearQuery($request->saleperson_id, $request->year))->handle();

            return response()->success($saleReportData, 'success', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function salepersonSaleReportWithKpiInYear(Request $request)
    {
        //abort_if(authorize('view_customer_payment', SaleReportPolicy::class), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // saleperson_id, year
        try {

            $saleReportData = (new FindSalepersonSaleReportWithKpiInYearQuery($request->saleperson_id, $request->year))->handle();

            return response()->success($saleReportData, 'success', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function companySaleReportWithKpiInYear(Request $request)
    {
        //abort_if(authorize('view_customer_payment', SaleReportPolicy::class), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // company_id, year
        try {

            $saleReportData = (new FindCompanySaleReportWithKpiInYearQuery($request->company_id, $request->year))->handle();

            return response()->success($saleReportData, 'success', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function uploadJobSheet(StoreJobSheetUploadRequest $request)
    {
        try {
            DB::beginTransaction();
            $saleReport = SaleReportEloquentModel::where('project_id', $request->project_id)->first();
            if ($saleReport) {
                if ($request->hasFile('document_file')) {
                    $saleReport->clearMediaCollection('document_file');
                    foreach ($request->file('document_file') as $file) {
                        if ($file->isValid()) {
                            $saleReport->addMedia($file)
                                ->toMediaCollection('document_file', 'jobsheet_documents');
                        }
                    }
                }
            }
            $saleReport->update([
                'file_status' => 'UPLOADED'
            ]);
            DB::commit();
            return response()->success($saleReport, 'success', Response::HTTP_OK);
        } catch (\Exception $error) {
            DB::rollBack();
            return response()->error($error->getMessage(), 'error', Response::HTTP_EXPECTATION_FAILED);
        }
    }

    public function getPendingApprovalDocuments(Request $request)
    {
        try {
            $filters = $request->all();
            $data = (new GetPendingApproveDocumentQuery($filters))->handle();

            return response()->success($data, 'success', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function getManagerPendingApprovalDocuments(Request $request)
    {
        try {
            $filters = $request->all();
            $data = (new GetManagerPendingApproveDocumentQuery($filters))->handle();

            return response()->success($data, 'success', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function approveDocument($id)
    {
        try {
            $saleReport = SaleReportEloquentModel::find($id);
            $saleReport->update(['file_status' => 'APPROVED']);

            return response()->success($saleReport, 'success', Response::HTTP_OK);
        } catch (\Exception $error) {
            return response()->error($error->getMessage(), 'error', Response::HTTP_EXPECTATION_FAILED);
        }
    }

    public function markClaimCommission($id, Request $request)
    {
        try {
            return response()->success((new UpdateMarkedSaleReportCommand($id, $request))->execute(), 'success', Response::HTTP_OK);
        } catch (\Exception $error) {
            return response()->error($error->getMessage(), 'error', Response::HTTP_EXPECTATION_FAILED);
        }
    }

    public function signSaleReport($id, Request $request)
    {
        try {
            $data = $request->all();
            $saleReport = (new SignSaleReportCommand($id, $data))->execute();
            return response()->success($saleReport, 'success', Response::HTTP_OK);
        } catch (\Exception $error) {
            return response()->error($error->getMessage(), 'error', Response::HTTP_EXPECTATION_FAILED);
        }
    }
}
