<?php

namespace Src\Company\Project\Presentation\API;

use Src\Common\Infrastructure\Laravel\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Src\Company\Project\Application\Mappers\SupplierCostingPaymentMapper;
use Src\Company\Project\Application\UseCases\Commands\StoreManagerSignSupplierCostingPaymentCommand;
use Src\Company\Project\Application\UseCases\Commands\StoreSupplierCostingPaymentCommand;
use Src\Company\Project\Application\UseCases\Commands\UpdateSupplierCostingWithPaymentIdCommand;
use Src\Company\Project\Application\UseCases\Queries\FindSupplierCostingPaymentByIdQuery;
use Src\Company\Project\Application\Policies\SaleReportPolicy;
use Src\Company\Project\Application\UseCases\Commands\CheckVendorInvoicesWithSameCompanyCommand;
use Src\Company\Project\Application\UseCases\Queries\GetAllSupplierCostingPaymentQuery;
use Src\Company\Project\Infrastructure\EloquentModels\ProjectEloquentModel;

use Src\Company\CompanyManagement\Domain\Services\QuickbookService;

class SupplierCostingPaymentController extends Controller
{
    private $quickBookService;

    public function __construct(QuickbookService $quickBookService)
    {
        $this->quickBookService = $quickBookService;
    }

    public function index(Request $request)
    {
        abort_if(authorize('view_pending_supplier_costing', SaleReportPolicy::class), Response::HTTP_FORBIDDEN, 'Need view_pending_supplier_costing permission for Supplier Costing!');

        try {

            $filters = $request->all();

            $supplierCostingPayment = (new GetAllSupplierCostingPaymentQuery($filters))->handle();

            return response()->success($supplierCostingPayment, 'success', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function SupplierCostingPaymentDetail(int $id)
    {
        abort_if(authorize('view_supplier_costing', SaleReportPolicy::class), Response::HTTP_FORBIDDEN, 'Need view_pending_supplier_costing permission for Supplier Costing');

        try {

            $supplierCostingPayment = (new FindSupplierCostingPaymentByIdQuery($id))->handle();

            return response()->success($supplierCostingPayment, 'success', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function store(Request $request)
    {
        if(count($request->vendor_invoices) > 0){

            $isSameCompany = (new CheckVendorInvoicesWithSameCompanyCommand($request->vendor_invoices));
        }

        if(!$isSameCompany){
            return response()->error("Vendor Invoices Must Be Same Company", Response::HTTP_BAD_REQUEST);
        }

        try {

            $supplierCostingPayment = SupplierCostingPaymentMapper::fromRequest($request);

            $vendorInvoiceIds = $request->vendor_invoices; 
            
            $data = (new StoreSupplierCostingPaymentCommand($supplierCostingPayment,$vendorInvoiceIds))->execute();

            (new UpdateSupplierCostingWithPaymentIdCommand($request->vendor_invoices, $data->id))->execute();

            return response()->success($data, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function managerSign(Request $request)
    {
        abort_if(authorize('sign_supplier_costing_manager', SaleReportPolicy::class), Response::HTTP_FORBIDDEN, 'Need sign_pending_supplier_costing permission for Supplier Costing');

        try {

            (new StoreManagerSignSupplierCostingPaymentCommand($request))->execute();

            return response()->success(null, 'success', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function downloadPdf(Request $request)
    {
        $id = $request->id;
        
        $supplierCostingPayment = (new FindSupplierCostingPaymentByIdQuery($id))->handle();

        $folder_name  = config('app.folder_name');

        $projectId = $supplierCostingPayment->supplierCostings[0]->project_id;

        $managerName = $supplierCostingPayment->manager->first_name .' '. $supplierCostingPayment->manager->last_name;
        $managerNo = $supplierCostingPayment->manager->contact_no;
        $managerSign = base64_encode(file_get_contents(storage_path('app/public/supplier_costing/' . $supplierCostingPayment->manager_signature)));
        $accountant = $supplierCostingPayment->accountant->first_name .' '. $supplierCostingPayment->accountant->last_name;

        if($supplierCostingPayment->payment_method == 1){
            $paymentMethod = "TT";
        } else if ($supplierCostingPayment->payment_method == 2){
            $paymentMethod = "CASH";
        } else if ($supplierCostingPayment->payment_method == 3){
            $paymentMethod = "PAYNOW";
        } else if ($supplierCostingPayment->payment_method == 4){
            $paymentMethod = "CHEQUE";
        } else if ($supplierCostingPayment->payment_method == 5){
            $paymentMethod = "NET";
        }

        if($supplierCostingPayment->payment_type === 1){
            $paymentType = "Deposit Payment";
        }elseif ($supplierCostingPayment->payment_type === 2) {
            $paymentType = "1st Payment";
        }elseif ($supplierCostingPayment->payment_type === 3) {
            $paymentType = "2nd Payment";
        }elseif ($supplierCostingPayment->payment_type === 4) {
            $paymentType = "3rd Payment";
        }elseif ($supplierCostingPayment->payment_type === 5) {
            $paymentType = "Final Payment";
        }

        $data = [
            "documentData" => $supplierCostingPayment,
            "folder_name" => $folder_name,
            "company_logo" => $this->getCompanyLogo($projectId),
            "managerName" => $managerName,
            "managerNo" => $managerNo,
            "managerSign" => $managerSign,
            "accountant" => $accountant,
            "paymentMethod" => $paymentMethod,
            "paymentType" => $paymentType
        ];

        $pdf = \PDF::loadView('pdf.VendorPayment.vendor_payment', $data);

        $fileName = 'vendor_payments' . time() . '.pdf';

        return $pdf->download($fileName);
    }

    public function getCompanyLogo($projectId)
    {
        $project = ProjectEloquentModel::with('company')->find($projectId);

        $company_logo = $project->company->logo;

        $customer_file_path = 'logo/' . $company_logo;

        $company_image = Storage::disk('public')->get($customer_file_path);

        $company_base64Image = base64_encode($company_image);

        return $company_base64Image;
        
    }

    public function getAllAccount()
    {
        $accounts = $this->quickBookService->getAllAccount();

        return response()->success($accounts, 'success', Response::HTTP_OK);
    }

}
