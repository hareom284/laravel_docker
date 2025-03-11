<?php

namespace Src\Company\Document\Presentation\API;

use DateTime;
use stdClass;
use Exception;
use Carbon\Carbon;
use setasign\Fpdi\Fpdi;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\CompanyManagement\Domain\Repositories\AccountingServiceInterface;
use Src\Company\CompanyManagement\Domain\Services\QuickbookService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Src\Company\System\Domain\Mail\NotifySalepersonMail;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Src\Company\Document\Application\Mappers\ContractMapper;
use Src\Company\Document\Domain\Mail\NotifyDocumentSignEmail;
use Src\Company\Project\Domain\Resources\ProjectDetailResource;
use Src\Company\Document\Domain\Resources\RenovationItemsResource;
use Src\Company\Document\Application\Requests\SendEmailCopyRequest;
use Src\Company\Document\Domain\Model\Entities\RenovationDocuments;
use Src\Company\Document\Application\Policies\ProjectQuotationPolicy;
use Src\Company\Document\Application\Mappers\RenovationDocumentsMapper;
use Src\Company\Project\Application\UseCases\Queries\FindProjectByIdQuery;
use Src\Company\Document\Application\Requests\StoreProjectQuotationRequest;
use Src\Company\Project\Infrastructure\EloquentModels\ProjectEloquentModel;
use Src\Company\Document\Application\UseCases\Commands\SendEmailCopyCommand;
use Src\Company\Document\Application\UseCases\Commands\StoreContractCommand;
use Src\Company\Document\Application\UseCases\Queries\GetPendingRenoDocQuery;
use Src\Company\Document\Infrastructure\EloquentModels\AOWIndexEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\ContractEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\SectionsEloquentModel;
use Src\Company\Document\Application\UseCases\Commands\DeleteQuotationCommand;
use Src\Company\Project\Infrastructure\EloquentModels\SaleReportEloquentModel;
use Src\Company\Document\Application\UseCases\Commands\ChangeLeadStatusCommand;
use Src\Company\Document\Infrastructure\EloquentModels\ItemsIndexEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\SectionsIndexEloquentModel;
use Src\Company\System\Application\UseCases\Queries\FindGeneralSettingByNameQuery;
use Src\Company\Document\Application\UseCases\Queries\FindRenovationDocumentsIndex;
use Src\Company\Document\Infrastructure\EloquentModels\RenovationItemsEloquentModel;
use Src\Company\Document\Application\UseCases\Commands\StoreRenovationDocumentCommand;
use Src\Company\Document\Application\UseCases\Queries\FindAllRenovationDocumentsQuery;
use Src\Company\Document\Application\UseCases\Queries\FindTemplateItemsForUpdateQuery;
use Src\Company\Document\Infrastructure\EloquentModels\RenovationSettingEloquentModel;
use Src\Company\Project\Application\UseCases\Commands\StoreTermAndConditionSignatures;
use Src\Company\CustomerManagement\Infrastructure\EloquentModels\CustomerEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\RenovationSectionsEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\RenovationDocumentsEloquentModel;
use Src\Company\Document\Application\UseCases\Commands\StoreRenovationCustomerSignCommand;
use Src\Company\Document\Application\UseCases\Commands\UpdateCompanyInvoiceStartNoCommand;
use Src\Company\Document\Application\UseCases\Queries\GetRenovationItemsWithSectionsQuery;
use Src\Company\Document\Application\UseCases\Queries\FindSelectedRenovationDocumentsQuery;
use Src\Company\Document\Application\UseCases\Commands\StoreRenovationItemScheduleByDocumentIdCommand;
use Src\Company\System\Infrastructure\EloquentModels\GeneralSettingEloquentModel;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;

use Src\Company\Document\Application\Requests\StoreSingedDocumentUploadRequest;
use Src\Company\Document\Application\UseCases\Commands\UpdateQuotationDetailCommand;
use Src\Company\Document\Infrastructure\EloquentModels\VendorEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\CustomerPaymentEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\SupplierCostingEloquentModel;

class RenovationDocumentController extends Controller
{
    protected $original_quantities;
    private $quickBookService;
    private $accountingService;

    public function __construct(QuickbookService $quickBookService = null, AccountingServiceInterface $accountingService = null)
    {
        $this->quickBookService = $quickBookService;
        $this->accountingService = $accountingService;
    }

    public function index($project_id, $renovation_type)
    {
        abort_if(authorize('view', ProjectQuotationPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Renovation Document!');

        $data = (new FindRenovationDocumentsIndex($project_id, $renovation_type))->handle();

        return response()->json($data, Response::HTTP_OK);
    }

    public function getRenovationItemsWithSections($projectId)
    {
        abort_if(authorize('view', ProjectQuotationPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Renovation Document!');

        try {

            $renovationItems = (new GetRenovationItemsWithSectionsQuery($projectId))->handle();

            return response()->success($renovationItems, 'success', Response::HTTP_OK);
        } catch (\DomainException $domainException) {

            return response()->error($domainException->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function getPendingRenoDoc(Request $request)
    {
        try {

            $filters = $request->all();

            $renoDocLists = (new GetPendingRenoDocQuery($filters))->handle();

            return response()->success($renoDocLists, 'success', Response::HTTP_OK);
        } catch (\DomainException $domainException) {

            return response()->error($domainException->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function show($id, $type)
    {
        abort_if(authorize('view', ProjectQuotationPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Renovation Document!');

        try {

            logger('htlleo ',[''=> $id]);
            $renovation_documents_data = (new FindAllRenovationDocumentsQuery($id, $type))->handle();

            return response()->json($renovation_documents_data, Response::HTTP_OK);
        } catch (\DomainException $domainException) {
            return response()->json(['error' => $domainException->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function remove($id, $project_id)
    {
        abort_if(authorize('destroy', ProjectQuotationPolicy::class), Response::HTTP_FORBIDDEN, 'Need destroy permission for Renovation Document!');

        try {

            $hasStatus = RenovationDocumentsEloquentModel::where([
                ['project_id', $project_id],
                ['type', 'QUOTATION'],
                ['deleted_at', null],
                ['signed_date', '!=', null]
            ])->exists();

            if(!$hasStatus)
            {
                (new DeleteQuotationCommand($id))->execute();

                return response()->json("Success Delete", Response::HTTP_OK);
            }else{
                return response()->json(['error' => "You can't delete signed version.Or its already deleted."], Response::HTTP_BAD_REQUEST);
            }

        } catch (\DomainException $domainException) {
            return response()->json(['error' => $domainException->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function showTemplate($document_id)
    {
        abort_if(authorize('view', ProjectQuotationPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Renovation Document!');

        try {

            $renovation_documents_data = (new FindSelectedRenovationDocumentsQuery($document_id))->handle();

            return response()->json($renovation_documents_data, Response::HTTP_OK);
        } catch (\DomainException $domainException) {
            return response()->json(['error' => $domainException->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function store(StoreProjectQuotationRequest $request): JsonResponse
    {
        //check if user's has permission
        abort_if(authorize('store', ProjectQuotationPolicy::class), Response::HTTP_FORBIDDEN, 'Need store permission for Renovation Document!');

        try {
            DB::beginTransaction();
            $enableDataMigrationMode = GeneralSettingEloquentModel::where('setting', "enable_data_migration_mode")
            ->where('value', "true")
            ->first();
            $data = json_decode($request->data);
            // $totalForLump = json_decode($request->totalForLump);
            $renovation_documents = RenovationDocumentsMapper::fromRequest($request, $request->reno_document_id);
            $renovation_documents_data = (new StoreRenovationDocumentCommand($renovation_documents, $data))->execute();

            if($request->has('hide_total') && $request->hide_total == 'true')
            {
                $hide_total_exist_status = RenovationSettingEloquentModel::where([
                    'renovation_document_id' => $renovation_documents_data->id,
                    'setting' => 'hide_total_in_print'
                ])->exists();

                if(!$hide_total_exist_status)
                {
                    RenovationSettingEloquentModel::create([
                        'renovation_document_id' => $renovation_documents_data->id,
                        'setting' => 'hide_total_in_print',
                        'value' => 'true'
                    ]);
                }
            }

            if($enableDataMigrationMode){
                $request['id'] = $renovation_documents_data->id;
                $request['our_ref'] = $request->our_ref;
                $request['document_type'] = $request->document_type;
                $request['date'] = $request->date;
                $this->sign($request);
            } else {
                $request['renovation_document_id'] = $renovation_documents_data->id;
                $request['project_id'] = $request->project_id;
                $request['pdf_status'] = "SAVE";
                $request['type'] = $request->type;
                $this->downloadPdf($request);
            }
            DB::commit();
            return response()->json($renovation_documents_data, Response::HTTP_CREATED);
        } catch (\DomainException $domainException) {
            return response()->json(['error' => $domainException->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        } catch (Exception $ex) {
            DB::rollBack();
            throw $ex; // Handle exception as needed
        }
    }

    // StoreRenovationDocumentSignRequest
    public function sign(Request $request)
    {
        //check if user's has permission
        abort_if(authorize('sign', ProjectQuotationPolicy::class), Response::HTTP_FORBIDDEN, 'Need sign permission for Renovation Document!');
        DB::beginTransaction();
        try {

            (new StoreRenovationCustomerSignCommand($request->all()))->execute();

            //change customer status from lead to home owner
            (new ChangeLeadStatusCommand($request->id))->execute();

            $renovation_document = RenovationDocumentsEloquentModel::with('renovation_items')->where('id', $request->id)->first();

            $renovation_document->renovation_items()->update(['active' => true]);

            $projectId = RenovationDocumentsEloquentModel::find($request->id)->project_id;

            $project = ProjectEloquentModel::find($projectId);

            // For agrement_no
            $documentTypeShortName = '';
            $checkCommonQuotationNumSetting = GeneralSettingEloquentModel::where('setting', "enable_common_quotation_running_number")
                ->where('value', "true")
                ->first();
            $checkCommonProjectNumSetting = GeneralSettingEloquentModel::where('setting', "enable_common_project_running_number")
            ->where('value', "true")
            ->first();
            $enableDataMigrationMode = GeneralSettingEloquentModel::where('setting', "enable_data_migration_mode")
            ->where('value', "true")
            ->first();
            $common_project_running_number = 0;
            $common_quotation_running_number = 0;
            $runningNum = $project->company->invoice_no_start;
            $project_inv_no = $runningNum;
            if ($checkCommonProjectNumSetting) {
                $commonPjNum = GeneralSettingEloquentModel::where('setting', 'common_project_start_number')->first();
                $common_project_running_number = $commonPjNum->value;
                $project_inv_no = $common_project_running_number ?? 0;
            }

            if ($checkCommonQuotationNumSetting) {
                $commonQONum = GeneralSettingEloquentModel::where('setting', 'common_quotation_start_number')->first();
                $common_quotation_running_number = $commonQONum->value;
            }

            // if($checkCommonProjectNumSetting){
            //     $commonPjNum = GeneralSettingEloquentModel::where('setting','common_project_start_number')->first();
            //     $runningNum = $commonPjNum->value;
            // }else{
            // }

            $salesperson = $project->salespersons->first();
            $name = $salesperson->first_name . ' ' . $salesperson->last_name;

            $initialSalespersonName = implode("", array_map(fn($word) => $word[0], explode(" ", $name)));
            $block_num = $project->property->block_num;
            $block_num = str_replace(array('Blk', 'Blk ', 'Block', 'Block ', 'blk', 'blk ', 'BLK', 'BLK ', 'BLOCK', 'BLOCK '), '', $block_num);

            $documentNumber = 0;

            switch ($request->type) {
                case 'QUOTATION':

                    $documentTypeShortName = 'QO';
                    if($enableDataMigrationMode){
                        $projectAgreementNum = $request->our_ref;
                        $project->update([
                            'agreement_no' => $projectAgreementNum,
                            'project_status' => "InProgress",
                        ]);
                    } else {
                        $projectAgreementNum = generateAgreementNumber('signed_project', [
                            'company_initial' => $project->company->docu_prefix,
                            'quotation_initial' => $project->company->quotation_prefix,
                            'salesperson_initial' => $initialSalespersonName,
                            'block_num' => $block_num,
                            'date' => Carbon::now()->toDateString(),
                            'running_num' => $runningNum,
                            'project_id' => $project->id,
                            'quotation_num' => $project->company->quotation_no,
                            'project_agr_no' => $project->agreement_no,
                            'common_project_running_number' => $common_project_running_number,
                            'common_quotation_running_number' => $common_quotation_running_number
                        ]);

                        //end change
                        $project->update([
                            'invoice_no' => $project_inv_no,
                            'agreement_no' => $projectAgreementNum,
                            'project_status' => "InProgress"
                        ]);
                        (new UpdateCompanyInvoiceStartNoCommand($project->company_id, ++$runningNum))->execute();
                    }


                    $contracts = ContractEloquentModel::where('project_id', $projectId)->exists();

                    if ($contracts) {
                        ContractEloquentModel::where('project_id', $projectId)->update([
                            'contract_sum' => $renovation_document->total_amount,
                        ]);
                    }

                    //Fixed bugs when sale report is not created in the project create process
                    $projectSaleReport = SaleReportEloquentModel::where('project_id', $projectId)->first();

                    if(is_null($projectSaleReport)){

                        SaleReportEloquentModel::create([
                            'project_id' => $projectId,
                            'total_sales' => $renovation_document->total_amount,
                            'remaining' => $renovation_document->total_amount
                        ]);

                    }else{

                        $projectSaleReport->update([
                            'total_sales' => $renovation_document->total_amount,
                            'remaining' => $renovation_document->total_amount
                        ]);
                    }

                    foreach ($renovation_document->renovation_items as $item) {
                        if (!$item->prev_item_id) {
                            RenovationSectionsEloquentModel::find($item->renovation_item_section_id)->increment('total_items_count');
                        }
                    }

                    // This is the part where quote value and book value are updated

                    // Only update the customer from the first room of the array
                    if(isset($request->customer_signature[0]['customer_id'])){
                        $customer = CustomerEloquentModel::where('user_id', $request->customer_signature[0]['customer_id'])->first();

                        $customer->update([
                            'quote_value' => $renovation_document->total_amount,
                            'book_value' => $renovation_document->total_amount,
                        ]);
                    } else {
                        $project = ProjectEloquentModel::with('properties', 'salespersons', 'customer')->find($projectId);
                        $customer = CustomerEloquentModel::where('user_id', $project->customer_id)->first();
                        $customer->update([
                            'quote_value' => $renovation_document->total_amount,
                            'book_value' => $renovation_document->total_amount,
                        ]);
                    }

                    $generalSettingEloquent = GeneralSettingEloquentModel::where('setting', 'accounting_software_integration')->first();

                    if($generalSettingEloquent && $generalSettingEloquent->value != 'none'){

                        if($enableDataMigrationMode){

                            $agrNo = $request->our_ref;

                            if($generalSettingEloquent->value == 'quickbooks'){

                                $classFromQbo = $this->accountingService->getProjectByName($project->company_id, $agrNo);

                                $project->update([
                                    'quickbook_class_id' => $classFromQbo->Id
                                ]);

                                $saleReport = SaleReportEloquentModel::where('project_id', $project->id)->first();

                                $customerPayments = CustomerPaymentEloquentModel::where('remark', $classFromQbo->Id)->get();

                                foreach ($customerPayments as $customerPayment) {

                                    $unpaidPdf = $this->accountingService->saveInvoicePdf($project->company_id, $customerPayment->quick_book_invoice_id);

                                    $customerPayment->unpaid_invoice_file_path = $unpaidPdf;
                                    $customerPayment->sale_report_id = $saleReport->id;
                                    $customerPayment->customer_id = $customer->user->id;

                                    $customerPayment->save();
                                }

                                $supplierCostings = SupplierCostingEloquentModel::where('remark', $classFromQbo->Id)->get();

                                foreach($supplierCostings as $supplierCosting) {

                                    $vendorFromQbo = $this->accountingService->getVendorById($project->company_id, $supplierCosting->vendor_id);

                                    $vendor = VendorEloquentModel::where('name', $vendorFromQbo->DisplayName)->first();

                                    $supplierCosting->vendor_id = $vendor->id;
                                    $supplierCosting->project_id = $project->id;

                                    $supplierCosting->save();
                                }
                            }

                        }else{
                            $customerFirstName = $customer->user->first_name;
                            $customerLastName = $customer->user->last_name;

                            if(is_null($customerLastName) || $customerLastName == ''){
                                $customerName = $customerFirstName;
                            }else{
                                $customerName = $customerFirstName . ' ' . $customerLastName;
                            }

                            $type = $customer->customer_type ? 1 : 0;

                            if($this->accountingService){

                                $quickBookCustomerId = $this->accountingService->getCustomer($project->company_id,$customerName);

                                if (!$quickBookCustomerId) {

                                    $customerEmail = $customer->user->email;
                                    $customerNo = $customer->user->contact_no;
                                    $address = $project->property->block_num . ' ' . $project->property->street_name . ' ' . $project->property->unit_num;
                                    $postalCode = $project->property->postal_code;

                                    $customerData = [
                                        'name' => $customerName,
                                        'first_name' => $customer->user->first_name,
                                        'last_name' => $customer->user->last_name,
                                        'companyName' => ($type === 1) ? $customerName : null,
                                        'email' => $customerEmail,
                                        'address' => $address,
                                        'postal_code' => $postalCode,
                                        'contact_no' => $customerNo
                                    ];

                                    $qboRecentCusomterId = $this->accountingService->storeCustomer($project->company_id,$customerData);

                                    if($generalSettingEloquent->value == 'quickbooks'){

                                        UserEloquentModel::find($customer->user_id)->update([
                                            'quick_book_user_id' => $qboRecentCusomterId
                                        ]);

                                    }else{

                                        UserEloquentModel::find($customer->user_id)->update([
                                            'xero_user_id' => $qboRecentCusomterId
                                        ]);
                                    }
                                } else {

                                    if($generalSettingEloquent->value == 'quickbooks'){

                                        UserEloquentModel::find($customer->user_id)->update([
                                            'quick_book_user_id' => $quickBookCustomerId
                                        ]);

                                    }else{

                                        UserEloquentModel::find($customer->user_id)->update([
                                            'xero_user_id' => $quickBookCustomerId
                                        ]);
                                    }
                                }

                                if($generalSettingEloquent->value == 'quickbooks'){

                                    Log::info('Project Agr No : ' . $projectAgreementNum);

                                    $qboClass = $this->accountingService->storeClass($project->company_id, $projectAgreementNum);

                                    $project->update([
                                        'quickbook_class_id' => $qboClass->Id,
                                    ]);
                                }
                            }
                        }
                    }

                    $companyName = config('folder.company_folder_name');

                    if ($companyName == 'Tag' || $companyName == 'Aplus' || $companyName == 'Tidplus' || $companyName == 'Intheory' || $companyName == 'Whst') {
                        $contractData = new Request();
                        $contractData->project_id = $project->id;
                        $contractData->name = $customer->user->first_name . ' ' . $customer->user->last_name;
                        $contractData->nric = $customer->nric;
                        $contractData->company = $project->company->name;
                        $contractData->law = '10 WOODLANDS SECTOR 2 S(737 727)';
                        $contractData->contractor_days = 0;
                        $contractData->termination_days = 0;
                        $contractData->address = $project->properties->block_num . ' ' . $project->properties->street_name . ' #' . $project->properties->unit_num . ' Singapore ' . $project->properties->postal_code;

                        $contract = ContractMapper::fromRequest($contractData);

                        $result = (new StoreContractCommand($contract))->execute();
                        $enableMultipleTermAndCondition = GeneralSettingEloquentModel::where('setting', "enable_multiple_term_and_conditions")
                        ->where('value', "true")
                        ->first();
                        if($enableMultipleTermAndCondition){
                            $termAndConditions = (new StoreTermAndConditionSignatures($result))->execute();
                        }
                        $contractController = new ContractController();
                        $contractController->downloadContractPdf($contractData);
                    }

                    break;

                case 'VARIATIONORDER':
                    $documentNumber = RenovationDocumentsEloquentModel::where('project_id', $projectId)->whereNotNull('signed_date')->where('type', 'VARIATIONORDER')->count();

                    $documentTypeShortName = 'VO';

                    $saleReport = SaleReportEloquentModel::where('project_id', $projectId)->first(['total_sales', 'remaining']);

                    SaleReportEloquentModel::where('project_id', $projectId)->update([
                        'total_sales' => $saleReport->total_sales + $renovation_document->total_amount,
                        'remaining' => $saleReport->remaining + $renovation_document->total_amount
                    ]);

                    foreach ($renovation_document->renovation_items as $item) {
                        if (!$item->prev_item_id) {
                            RenovationSectionsEloquentModel::find($item->renovation_item_section_id)->increment('total_items_count');
                        }
                    }

                    break;

                case 'FOC':
                    $documentNumber = RenovationDocumentsEloquentModel::where('project_id', $projectId)->whereNotNull('signed_date')->where('type', 'FOC')->count();

                    $documentTypeShortName = 'FOC';

                    foreach ($renovation_document->renovation_items as $item) {
                        if (!$item->prev_item_id) {
                            RenovationSectionsEloquentModel::find($item->renovation_item_section_id)->increment('total_items_count');
                        }
                    }

                    break;

                case 'CANCELLATION':
                    $documentNumber = RenovationDocumentsEloquentModel::where('project_id', $projectId)->whereNotNull('signed_date')->where('type', 'CANCELLATION')->count();

                    $documentTypeShortName = 'CN';

                    $saleReport = SaleReportEloquentModel::where('project_id', $projectId)->first(['total_sales', 'remaining']);

                    SaleReportEloquentModel::where('project_id', $projectId)->update([
                        'total_sales' => $saleReport->total_sales - $renovation_document->total_amount,
                        'remaining' => $saleReport->remaining - $renovation_document->total_amount
                    ]);

                    foreach ($renovation_document->renovation_items as $item) {
                        RenovationSectionsEloquentModel::find($item->renovation_item_section_id)->increment('total_items_count');
                    }

                default:
                    # code...
                    break;
            }

            //store into renovation item schedules table to use in project progress page
            (new StoreRenovationItemScheduleByDocumentIdCommand($request->id))->execute();
            $request['renovation_document_id'] = $renovation_document->id;
            $request['project_id'] = $projectId;
            $request['pdf_status'] = "SAVE";
            $request['type'] = $request->type;

            // Update renovation document's agreement number with current date
            if (!$enableDataMigrationMode) {
                if ($documentTypeShortName === 'QO') {
                    $agreementNum = $projectAgreementNum;
                } else {
                    $agreementNum = generateAgreementNumber('signed_renovation_document', [
                        'company_initial' => $project->company->docu_prefix,
                        'quotation_initial' => $project->company->quotation_prefix,
                        'salesperson_initial' => $initialSalespersonName,
                        'block_num' => $project->property->block_num ?? null,
                        'date' => Carbon::now()->toDateString(),
                        'document_type' => $documentTypeShortName . ($documentNumber > 0 ? $documentNumber : ''),
                        'running_num' => $project->company->invoice_no_start,
                        'version_num' => $renovation_document->version_number ?? '',
                        'project_id' => $project->id,
                        'quotation_num' => $project->company->quotation_no,
                        'project_agr_no' => $project->agreement_no,
                        'common_project_running_number' => $common_project_running_number,
                        'common_quotation_running_number' => $common_quotation_running_number
                    ]);
                }
                $renovation_document->agreement_no = $agreementNum;
            } else {
                $renovation_document->status = 'approved';
                $renovation_document->signed_date = $request->date;
            }
            $renovation_document->save();

            $this->downloadPdf($request);

            $mailgun = config('services.mailgun.secret');

            // fire email if mailgun exist in env
            if (isset($mailgun)) {
                $project = ProjectEloquentModel::with('properties', 'salespersons', 'customer')->find($projectId);

                foreach ($project->salespersons as $saleperson) {

                    $emailData = [
                        'address' => $project->properties->block_num . ' ' . $project->properties->street_name . ' #' . $project->properties->unit_num . ' Singapore ' . $project->properties->postal_code,
                        'customer' => $project->customer->first_name . ' ' . $project->customer->last_name,
                        'type' => $request->type,
                        'saleperson' => $saleperson->first_name . ' ' . $saleperson->last_name,
                    ];

                    Mail::to($saleperson->email)->send(new NotifyDocumentSignEmail($emailData));
                }
            }
            DB::commit();

            return response()->json('success sign', Response::HTTP_OK);
        } catch (\DomainException $domainException) {

            return response()->json(['error' => $domainException->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        } catch (Exception $ex) {
            DB::rollBack();
            throw $ex; // Handle exception as needed
        }
    }

    public function sendMail(SendEmailCopyRequest $request)
    {
        try {

            $projectId = $request->project_id;

            $email = $request->email;

            $file = $request->file('attachment');

            $attachment = file_get_contents($file->getRealPath());

            (new SendEmailCopyCommand($projectId, $email, $attachment))->execute();

            return response()->success(null, 'success', Response::HTTP_OK);
        } catch (\DomainException $domainException) {
            return response()->json(['error' => $domainException->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function templateForUpdate($renovation_document_id)
    {
        //check if user's has permission
        abort_if(authorize('store', ProjectQuotationPolicy::class), Response::HTTP_FORBIDDEN, 'Need store permission for Renovation Document!');

        try {
            $result = (new FindTemplateItemsForUpdateQuery($renovation_document_id))->handle();

            return response()->json($result, Response::HTTP_CREATED);
        } catch (\DomainException $domainException) {
            return response()->json(['error' => $domainException->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
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

    public function getCompanyStamp($companyStamp)
    {
        if ($companyStamp) {
            $customer_file_path = 'stamp/' . $companyStamp;

            $company_image = Storage::disk('public')->get($customer_file_path);

            $company_base64Image = base64_encode($company_image);
            return $company_base64Image;
        }
    }

    function calculateTotalAllAmount($quotationLists, $gst_percentage)
    {

        $percentage = $quotationLists->special_discount_percentage;
        $sectionAllAmount = $quotationLists->section_total_amount;
        $totalAllAmountValue = 0;
        foreach ($sectionAllAmount as $i) {
            $totalAllAmountValue += floatval($i['total_price']);
        }

        $total_all_amount = $totalAllAmountValue;

        $total_discount =
            $total_all_amount == 0
            ? 0
            : $total_all_amount * (floatval($percentage) / 100);
        $total_special_discount =
            $total_all_amount == 0
            ? 0
            : $total_all_amount - $total_all_amount * (floatval($percentage) / 100);

        $total_gst =
            $total_all_amount == 0
            ? 0
            : $total_special_discount * ($gst_percentage / 100);

        $total_inclusive =
            $total_all_amount == 0
            ? 0
            : $total_special_discount + $total_gst;

        $total_amount = $total_all_amount - $total_discount;
        $only_discount_amount = $total_all_amount - $total_special_discount;
        return [
            'gst_percentage' => $gst_percentage,
            'discount_percentage' => $percentage,
            'total_all_amount' => $total_all_amount,
            'total_special_discount' => $total_special_discount,
            'total_gst' => $total_gst,
            'total_inclusive' => $total_inclusive,
            'only_discount_amount' => $only_discount_amount,
            'total_discount' => $total_discount,
            'total_amount' => $total_amount
        ];
    }

    public function calculateTotalAllAmountVO($quotationLists, $gst_percentage)
    {
        //$total all amount is the sum before gst
        $percentage = $quotationLists->special_discount_percentage;
        $totalAllAmount = $quotationLists->section_total_amount->sum('total_price');
        $totalSpecialDiscount = $totalAllAmount * (100 - $percentage) / 100;
        $totalGST = $totalSpecialDiscount * ($gst_percentage / 100);

        $totalInclusive = $totalGST +  $totalSpecialDiscount;
        $only_discount_amount = $quotationLists->section_total_amount->sum('total_price') * (100 - $percentage) / 100;

        $total_discount =
            $totalAllAmount == 0
            ? 0
            : $totalAllAmount * (floatval($percentage) / 100);
        $total_amount = $totalAllAmount - $total_discount;
        // Assuming you want to return these values as an associative array
        return [
            'gst_percentage' => $gst_percentage,
            'discount_percentage' => $percentage,
            'total_inclusive' => $totalInclusive,
            'total_gst' => $totalGST,
            'total_special_discount' => $totalSpecialDiscount,
            'total_all_amount' => $totalAllAmount,
            'only_discount_amount' => $only_discount_amount,
            'total_amount' => $total_amount,
            'total_discount' => $total_discount
        ];
    }

    function sortArrayBySequence($originalArray, $sequenceArray)
    {
        $indexedArray = array_combine(range(0, count($originalArray) - 1), $originalArray);

        // Sort the associative array based on the sequence array
        $sortedAssocArray = [];
        foreach ($sequenceArray as $value) {
            $key = array_search($value, $indexedArray);
            if ($key !== false) {
                $sortedAssocArray[$key] = $value;
            }
        }

        // Return the values of the sorted associative array
        return array_values($sortedAssocArray);
    }

    function sortingOfQuotation($id, $type, $enable_cn_in_vo_status)
    {
        $quotationLists = (new FindAllRenovationDocumentsQuery($id, $type))->handle(); // Assuming $quotationLists is a collection and has an 'items' collection.

        if ($type == 'VARIATIONORDER') {

            $projectId = $quotationLists->project_id;

            $sign_quotation_id = RenovationDocumentsEloquentModel::where('project_id', $projectId)
                ->where('type', 'QUOTATION')
                ->whereNotNull('signed_date')
                ->pluck('id')
                ->first();

            $lastSignedVO = RenovationDocumentsEloquentModel::where('project_id', $projectId)
                ->where('type', 'VARIATIONORDER')
                ->whereNotNull('signed_date')
                ->pluck('id')
                ->last();

            $original_signed_quotaiton_lists = (new FindAllRenovationDocumentsQuery($sign_quotation_id, 'QUOTATION'))->handle();
            if ($lastSignedVO) {
                $renoItems = RenovationItemsEloquentModel::where('renovation_document_id', $lastSignedVO)
                    ->pluck('quantity', 'quotation_template_item_id')
                    ->toArray();
            } else {
                $renoItems = [];
            }

            $this->original_quantities = $original_signed_quotaiton_lists->renovation_items
                ->pluck('quantity', 'quotation_template_item_id')
                ->toArray();

            $this->original_quantities = $renoItems + $this->original_quantities;

            $items = $quotationLists->renovation_items;

            $renovation_items = $items;
        } else {
            $renovation_items = $quotationLists->renovation_items;
        }

        //end handle

        // $renovation_items = $quotationLists->renovation_items; //old one
        $data = $renovation_items->map(function ($item) use($enable_cn_in_vo_status, $type) {

            $areaOfWorkName = isset($item->renovation_area_of_work->name)
                ? $item->renovation_area_of_work->name
                : ($item->renovation_item_area_of_work_id
                    ? $item->renovation_area_of_work->areaOfWork->name
                    : '');

            return [
                'id' => $item->id,
                'quotation_template_item_id' => $item->quotation_template_item_id,
                'parent_id' => $item->parent_id,
                'name' => $item->name,
                'sub_description' => $item->sub_description,
                'calculation_type' => $item->renovation_sections->calculation_type,
                'quantity' => $item->quantity,
                'current_quantity' => isset($item->current_quantity) ? $item->current_quantity : null,
                'length' => $item->length,
                'breadth' => $item->breadth,
                'height' => $item->height,
                'measurement' => $item->unit_of_measurement,
                'is_fixed_measurement' => $item->is_fixed_measurement,
                'price' => $item->price,
                'cost_price' => $item->cost_price,
                'profit_margin' => $item->profit_margin,
                'is_FOC' => $item->is_FOC,
                'is_CN' => $item->is_CN,
                'is_page_break' => $item->is_page_break,
                'section_id' => $item->renovation_sections->section_id,
                'section_name' => $item->renovation_sections->name,
                'area_of_work_id' => $item->renovation_area_of_work->section_area_of_work_id,
                'area_of_work_name' => $areaOfWorkName,
                'is_excluded' => $item->is_excluded,
                'is_page_break' => $type == 'VARIATIONORDER' && $enable_cn_in_vo_status ? $item->is_page_break : $item?->renovation_sections?->is_page_break ?? false
            ];
        });

        $sortQuotation = [];
        // Getting unique section IDs. pluck() gets the values of a given key (column):
        $sectionIds = [];

        foreach ($data as $item) {
            $sectionIds[] = $item['section_id'];
        }

        $originalSections = collect($sectionIds)->unique()->toArray();
        $sectionIndexArray = SectionsIndexEloquentModel::where('document_id', $id)
            ->pluck('section_sequence')
            ->first();

        $sectionIndexArray = json_decode($sectionIndexArray);

        $uniqueSections = $this->sortArrayBySequence($originalSections, $sectionIndexArray);

        foreach ($uniqueSections as $sectionId) {
            $filteredItemsBySectionId = $data->filter(function ($item) use ($sectionId) {
                return $item['section_id'] == $sectionId;
            });

            $emptyAOWItems = $filteredItemsBySectionId->whereNull('area_of_work_id');
            $filterAOWItems = $filteredItemsBySectionId->whereNotNull('area_of_work_id');

            $originalAOWItems = $filterAOWItems->pluck('area_of_work_id')->unique()->toArray();

            $aowIndexData = AOWIndexEloquentModel::where('document_id', $id)
                ->where('section_id', $sectionId)
                ->pluck('aow_sequence')
                ->first();

            $aowIndexArray = json_decode($aowIndexData);
            $uniqueAOWIds = $this->sortArrayBySequence($originalAOWItems, $aowIndexArray);

            $hasAOWItems = [];


            $itemsIndexData = ItemsIndexEloquentModel::where('document_id', $id)
                ->whereIn('aow_id', $uniqueAOWIds)
                ->get()
                ->groupBy('aow_id');

            foreach ($uniqueAOWIds as $aowId) {

                $itemIndexArray = json_decode(optional($itemsIndexData->get($aowId)->first())->items_sequence ?? '[]');


                $aowItems = $filterAOWItems->filter(function ($item) use ($aowId) {
                    return $item['area_of_work_id'] == $aowId;
                })->sortBy(function ($item) use ($itemIndexArray) {
                    return array_search($item['quotation_template_item_id'], $itemIndexArray);
                });

                if ($aowItems->isNotEmpty()) {
                    $firstAowItem = $aowItems->first(); // Getting the first item to extract AOW details
                    $aowItems = $this->organizeItemsByParent($aowItems);
                    $objForAOW = [
                        'area_of_work_id' => $aowId,
                        'area_of_work_name' => $firstAowItem['area_of_work_name'],
                        // 'area_of_work_items' => $aowItems->values()->all(), // this is david's code
                        'area_of_work_items' => $aowItems,
                    ];
                    $hasAOWItems[] = $objForAOW;
                }
            }

            $sectionObj = [
                'section_id' => $sectionId,
                'section_name' => $filteredItemsBySectionId->first()['section_name'],
                'section_total_price' => collect($quotationLists->section_total_amount)->where('section_id', $sectionId)->sum('total_price'),
                'emptyAOWData' => $emptyAOWItems->values()->all(),
                'hasAOWData' => $hasAOWItems,
                'is_page_break' => $filteredItemsBySectionId->first()['is_page_break'],
            ];

            $sortQuotation[] = $sectionObj;
        }

        return collect($sortQuotation);
    }

    // For sub items
    function organizeItemsByParent($items)
    {
        $itemMap = [];
        $result = [];

        // First pass: organize items by ID and find top-level items.
        foreach ($items as $item) {
            $item['items'] = [];  // Initialize a sub-items array.
            $itemMap[$item['quotation_template_item_id']] = $item;
            if (!$item['parent_id']) {
                $result[] = &$itemMap[$item['quotation_template_item_id']];
            }
        }

        // Second pass: assign sub-items to their parents.
        foreach ($items as $item) {
            if ($item['parent_id'] && isset($itemMap[$item['parent_id']])) {
                $itemMap[$item['parent_id']]['items'][] = &$itemMap[$item['quotation_template_item_id']];
            }
        }

        return $result;
    }

    function changeFormatDate($dateString)
    {
        try {
            $date = new DateTime($dateString);
            return $date->format('d/m/Y');
        } catch (Exception $e) {
            // Handle exception if the date cannot be parsed
            return null;
        }
    }

    function mergePdf($pdf1Path, $pdf2Path, $pdfDocument)
    {
        // Merge PDFs using FPDI
        $fPdf = new Fpdi();

        // Add pages from the first PDF
        $pageCount = $fPdf->setSourceFile($pdf1Path);
        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $fPdf->AddPage();
            $tplId = $fPdf->importPage($pageNo);
            $fPdf->useTemplate($tplId);
        }

        // Add pages from the second PDF
        $pageCount = $fPdf->setSourceFile($pdf2Path);
        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $fPdf->AddPage();
            $tplId = $fPdf->importPage($pageNo);
            $fPdf->useTemplate($tplId);
        }
        // Capture the merged PDF output as a string
        $mergedPdfContent = $fPdf->Output('S');

        $fileName = 'quotation_' . time() . '.pdf';
        // Save the PDF to a file in the 'public' disk
        $filePath = 'pdfs/' . $fileName;
        Storage::disk('public')->put($filePath, $mergedPdfContent);

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

        // Delete temporary files
        unlink($pdf1Path);
        unlink($pdf2Path);

        // Return the merged PDF for download
        return response()->download(storage_path('app/public/' . $filePath));
    }

    private function capturePdfOutput(Fpdi $pdf)
    {
        // Start capturing the output
        ob_start();
        $pdf->Output('S');
        $pdfContent = ob_get_contents();
        ob_end_clean();
        return $pdfContent;
    }

    function convertDate($dateString)
    {
        $originalFormat = 'd/m/Y';
        $date = DateTime::createFromFormat($originalFormat, $dateString);
        if ($date) {
            $formattedDate = $date->format('d M Y');
            return $formattedDate;
        } else {
            return '';
        }
    }

    function convertDateSupaspace($dateString)
    {
        try {
            $date = new DateTime($dateString);
            return $date->format('F j, Y');
        } catch (Exception $e) {
            // Handle exception if the date cannot be parsed
            return null;
        }
    }

    function convertSectionWithOrWithoutCN($sortData, $num)
    {
        $sections = collect($sortData);

        $data = $sections->map(function ($section) use($num) {

            $section_calculation_type = $section['hasAOWData'][0]['area_of_work_items'][0]['calculation_type'];

            // Filter area_of_work_items for each area_of_work
            $filteredHasAOWData = collect($section['hasAOWData'])->map(function ($aow) use($num) {
                $aow['area_of_work_items'] = collect($aow['area_of_work_items'])
                                            ->map(fn($item) => $this->filterItemsRecursively($item, $num))
                                            ->filter()
                                            ->values()
                                            ->all();
                return $aow;
            })->filter(fn($aow) => !empty($aow['area_of_work_items']))->all();

            // Only return section if it has area_of_work with CN items
            if (!empty($filteredHasAOWData)) {
                $section['hasAOWData'] = $filteredHasAOWData;
                $section['is_page_break'] = $section['hasAOWData'][0]['area_of_work_items'][0]['is_page_break'];
                // Calculate total price at the section level as sum of quantity * price across all items
                if($section_calculation_type == 'NORMAL')
                {
                    $section['section_total_price'] = collect($filteredHasAOWData)
                        ->flatMap(fn($aow) => $aow['area_of_work_items'])
                        ->sum(function ($item) use($num) {
                            return $this->calculateItemTotal($item, $num);
                     });
                }else{

                    if($num)
                    {

                        if(!str_contains((string) $section['section_total_price'], '-'))
                        {
                            $section['section_total_price'] = 0;
                        }

                    }else{
                        if(str_contains((string) $section['section_total_price'], '-'))
                        {
                            $section['section_total_price'] = 0;
                        }

                    }

                }

                return $section;
            }
        })->filter()->values();

        $sumOfTotalPrice = $data->sum('section_total_price');

        return [
            'data' => $data,
            'total' => $sumOfTotalPrice
        ];
    }

    private function calculateItemTotal($item, $num)
    {
        $totalSum = $item['quantity'] * $item['price'];

        $total = ($num && $totalSum < 0) || (!$num && $totalSum > 0) ? $totalSum : 0;

        if (isset($item['items']) && is_array($item['items'])) {

            $nestedTotal = collect($item['items'])->sum(function($nestedItem) use($num) {
                return $this->calculateItemTotal($nestedItem, $num);
            });

            $total += ($num && $nestedTotal < 0) || (!$num && $nestedTotal > 0) ? $nestedTotal : 0;
        }

        return $total;
    }

    private function filterItemsRecursively($item, $num)
    {
        $matchesCurrentItem = $item['is_CN'] == $num;

        if (isset($item['items']) && is_array($item['items'])) {
            $filteredSubItems = collect($item['items'])
                ->map(fn($nestedItem) => $this->filterItemsRecursively($nestedItem, $num))
                ->filter()
                ->values()
                ->all();

            $item['items'] = $filteredSubItems;

            if (count($filteredSubItems) > 0) {
                return $item;
            }

        }

        return $matchesCurrentItem ? $item : null;
    }

    public function downloadPdf(Request $request)
    {
        $id = $request->renovation_document_id;
        $project_id = $request->project_id;

        $printable = $request->printable;

        // $folder_name = $request->folder_name;
        $current_folder_name = config('folder.company_folder_name');
        $folder_name  = "";
        // $current_folder_name == 'Magnum' ? 'Twp' : $current_folder_name;
        if ($current_folder_name == 'Default') {
            $folder_name = 'Twp';
        } else if ($current_folder_name == 'Magnum') {
            $folder_name = 'Twp';
        } else if ($current_folder_name == 'Miracle') {
            $folder_name = 'Twp';
        } else if ($current_folder_name == 'Jream') {
            $folder_name = 'Twp';
        } else if ($current_folder_name == 'Paddry') {
            $folder_name = 'Twp';
        } else if ($current_folder_name == 'Molecule') {
            $folder_name = 'Twp';
        } else if ($current_folder_name == 'Henglai') {
            $folder_name = 'Twp';
        } else if ($current_folder_name == 'Optimum'){
            $folder_name = 'Twp';
        } else if ($current_folder_name == 'FiveFoot10'){
            $folder_name = 'Twp';
        }
        else {
            $folder_name = $current_folder_name;
        }

        $doc_type = $request->type;
        $project = (new FindProjectByIdQuery($project_id))->handle();
        $customers_array = $project->customersPivot->toArray();
        $quotationLists = (new FindAllRenovationDocumentsQuery($id, $doc_type))->handle();
        $enable_cn_in_vo_status = (new FindGeneralSettingByNameQuery('enable_cn_in_vo'))->handle();
        $sortData = $this->sortingOfQuotation($id, $doc_type, $enable_cn_in_vo_status->value == 'true');

        $version = '';
        $docTypeShortName = '';
        switch ($doc_type) {
            case 'QUOTATION':
                $version = '/QO';
                $docTypeShortName = 'QO';
                break;

            case 'VARIATIONORDER':
                $version = '/VO';
                $docTypeShortName = 'VO';
                break;

            case 'FOC':
                $version = '/FOC';
                $docTypeShortName = 'FOC';
                break;

            case 'CANCELLATION':
                $version = '/CN';
                $docTypeShortName = 'CN';
                break;

            default:
                # code...
                break;
        }

        if ($doc_type != 'QUOTATION') {
            $version_number = RenovationDocumentsEloquentModel::where('project_id', $project_id)
                ->where('type', $doc_type)
                ->get();

            foreach ($version_number as $key => $value) {

                if ($id == $value->id) {
                    $version .= (string)$key + 1;
                }
            }
        } else {
            if ($quotationLists->ver != null && $quotationLists->ver == 1) {
                $version .= $quotationLists->ver;
            } else {
                $version = $quotationLists->signed_date ? ' ' : $version . $quotationLists->ver;
            }
        }
        $enable_only_show_discount_amount = (new FindGeneralSettingByNameQuery('enable_only_show_discount_amount'))->handle();
        $enable_show_last_name_first = (new FindGeneralSettingByNameQuery('enable_show_last_name_first'))->handle();
        $enable_payment_terms = (new FindGeneralSettingByNameQuery('enable_payment_terms'))->handle();
        // $enable_cn_in_vo_status = (new FindGeneralSettingByNameQuery('enable_cn_in_vo'))->handle();
        $enable_show_selling_price = (new FindGeneralSettingByNameQuery('enable_show_selling_price_in_quotation_summary'))->handle();
        $enable_sub_description_feature = (new FindGeneralSettingByNameQuery('enable_sub_description_feature'))->handle();

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
            "company_name" => $project->customers->customers->company_name,
            "customer_type" => $project->customers->customers->customer_type
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
            "company_stamp" => $this->getCompanyStamp($project->company->company_stamp),
            "qr_code" => $project?->company?->qr_code ?? null,
            "tel" => $project->company->tel,
            "fax" => $project->company->fax
        ];
        // If quotation does not have agr no, display a default one in PDF
        if (empty($quotationLists->agreement_no))
            $agreementNo = $project->agreement_no . $version;
        else {
            $agreementNo = $quotationLists->agreement_no;
            if($project->project_status == 'InProgress' && $doc_type == 'QUOTATION'){
                // $agreementNo = preg_replace('/\/QO(?:[-\/]?\d+)?$/', '', $agreementNo);
                $agreementNo = $project->agreement_no;
            }
        }

        $headerFooterData = [
            'header' => $quotationLists->header_text,
            'footer' => $quotationLists->footer_text,
            'payment_terms_text' => $quotationLists->payment_terms_text,
            'saleperson_signature' => $quotationLists->salesperson_signature,
            'signed_date' => $quotationLists->signed_date ? $this->changeFormatDate($quotationLists->signed_date) : null,
            'signed_sale_email' => $quotationLists->signed_sale_email,
            'signed_sale_ph' => $quotationLists->signed_sale_ph,
            'signed_saleperson' => $quotationLists->signed_saleperson,
            // 'version_num' => $quotationLists->version_num,
            'version_num' => $version,
            'rank' => $quotationLists->rank,
            'disclaimer' => $quotationLists->disclaimer,
            'terms' => $quotationLists->terms,
            'customer_signature' => $quotationLists->customer_signature,
            'created_at' => $quotationLists->created_at ? $this->changeFormatDate($quotationLists->created_at) : null,
            'already_sign' => $quotationLists->already_sign,
            'project' => $project,
            'document_agreement_no' => $agreementNo,
            'customers_array' => $customers_array,
            'customers' => $customers,
            'properties' => $properties,
            'companies' => $companies,
            'folder_name' => $current_folder_name,
            'saleperson_id' => $quotationLists->saleperson_id,
            'customer_ids' => $quotationLists->customer_ids,
            'doc_type' => $doc_type,
            'enable_show_last_name_first' => $enable_show_last_name_first->value,
            'created_at_supaspace' => $quotationLists->created_at ? $this->convertDateSupaspace($quotationLists->created_at) : null,
            'signed_date_supaspace' => $quotationLists->signed_date ? $this->convertDateSupaspace($quotationLists->signed_date) : null,
            'salepersonRegistryNo' => $quotationLists->salepersonRegistry,
            'status' => $quotationLists->status,
            'project_status' => $project?->project_status ?? ''
        ];

        if ($doc_type == 'VARIATIONORDER') {
            $total_prices = $this->calculateTotalAllAmountVO($quotationLists, $companies['gst_percentage']);
        } else {
            $total_prices = $this->calculateTotalAllAmount($quotationLists, $companies['gst_percentage']);
        }

        $hide_total_exist_status = RenovationSettingEloquentModel::where([
            'renovation_document_id' => $id,
            'setting' => 'hide_total_in_print'
        ])->value('value') === 'true';

        $data = [
            'sortQuotation' => $sortData,
            'quotationList' => $quotationLists,
            'quotationData' => $headerFooterData,
            'total_prices' => $total_prices,
            'folder_name' => $folder_name,
            'doc_type'  => $doc_type,
            'hide_total' => $hide_total_exist_status,
            'current_folder_name' => $current_folder_name,
            'original_quantities' => isset($this->original_quantities) ? $this->original_quantities : [],
            'customers_array' => $project->customersPivot->toArray(),
            'settings' => [
                'enable_only_show_discount_amount' => $enable_only_show_discount_amount->value,
                'enable_show_last_name_first' => $enable_show_last_name_first->value,
                'enable_payment_terms' => $enable_payment_terms->value,
                'enable_cn_in_vo' => $enable_cn_in_vo_status->value === 'true',
                'enable_show_selling_price' => $enable_show_selling_price->value,
                'enable_sub_description_feature' => $enable_sub_description_feature->value
            ]
        ];

        if($doc_type == 'VARIATIONORDER' && GeneralSettingEloquentModel::where('setting', 'enable_cn_in_vo')->exists())
        {

            $enable_cn_in_vo = $enable_cn_in_vo_status->value === 'true';

            if ($enable_cn_in_vo) {
                $sectionWithCNData = [
                    'sectionWithoutCNItems' => $this->convertSectionWithOrWithoutCN($sortData, 0),
                    'sectionWithCNItems' => $this->convertSectionWithOrWithoutCN($sortData, 1)
                ];
                $data['sectionWithCNData'] = $sectionWithCNData;
                // $data['sectionWithCNItems'] = $this->convertSectionWithOrWithoutCN($sortData, 1);
                // $data['sectionWithoutCNItems'] = $this->convertSectionWithOrWithoutCN($sortData, 0);
            }

        }
        Log::info('data', $data);
        // return $data;

        $file = '';
        switch ($doc_type) {
            case 'QUOTATION':
                $file = '.quotation';
                break;
            case 'VARIATIONORDER':
                $file = '.variation_order';
                break;
            case 'CANCELLATION':
                $file = '.cancellation';
                break;
            case 'FOC':
                $file = '.foc';
                break;
            default:
                $file = '.quotation';
                break;
        }

        $headerHtml = view('pdf.Common.header', $headerFooterData)->render();
        $footerHtml = view('pdf.Common.footer', $headerFooterData)->render();

        try {
            $pdf = \PDF::loadView('pdf.' . $doc_type . '.' . $folder_name . $file, $data);
            // Log::channel('daily')->info('pdf.' . $doc_type . '.' . $folder_name . $file);
            $pdf->setOption('enable-javascript', true);
            if ($folder_name == 'Twp' && $current_folder_name == 'Miracle') {
                $pdf->setOption('margin-top', 108);
            } else if ($folder_name == 'Twp' && $current_folder_name == 'Henglai') {
                $pdf->setOption('margin-top', 85);
            } else if ($folder_name == 'Xcepcion') {
                $pdf->setOption('margin-top', 80);
            } else if ($folder_name == 'Metis') {
                $pdf->setOption('margin-top', 100);
            } else if ($folder_name == 'Artdecor') {
                $pdf->setOption('margin-top', 10);
            } else if ($folder_name == 'BuildSpec') {
                $pdf->setOption('margin-top', 40);
            } else if ($folder_name == 'Luxcraft') {
                $pdf->setOption('margin-top', 85);
            } else if ($folder_name == 'Makegood') {
                $pdf->setOption('margin-top', 10);
            } else if ($folder_name == 'Amp') {
                $pdf->setOption('margin-top', 80);
            } else if ($folder_name == 'Tag') {
                $pdf->setOption('margin-top', 68);
            } else if ($folder_name == 'Flynn') {
                $pdf->setOption('margin-top', 45);
            } else if ($folder_name == 'Brightway') {
                $pdf->setOption('margin-top', 5);
            } else if ($folder_name == 'Twp' && $current_folder_name == 'Molecule') {
                $pdf->setOption('margin-top', 95);
            } else if ($folder_name == 'Twp' && $current_folder_name == 'Henglai') {
                $pdf->setOption('margin-top', 95);
            } else if ($folder_name == 'Supaspace') {
                $pdf->setOption('margin-top', 90);
            } else if ($folder_name == 'Tidplus') {
                $pdf->setOption('margin-top', 45);
            } else if ($folder_name == 'Whst') {
                $pdf->setOption('margin-top', 35);
            } else if ($folder_name == 'Optimum') {
                $pdf->setOption('margin-top', 80);
            } else if ($folder_name == 'Blackalogy') {
                $pdf->setOption('margin-top', 75);
            } else if ($folder_name == 'SaltRoom'){
                $pdf->setOption('margin-top', 5);
            } else if ($folder_name == 'Praxis'){
                $pdf->setOption('margin-top', 15);
            } else if ($folder_name == 'Aplus') {
                $pdf->setOption('margin-top', 10);
            } else if ($folder_name == "Ideology") {
                $pdf->setOption('margin-top', 40);
            } else if ($folder_name == 'Intereno') {
                $pdf->setOption('margin-top', 120);
            } else if ($folder_name == 'AcCarpentry') {
                $pdf->setOption('margin-top', 110);
            } else if ($folder_name == 'Dorlich') {
                $pdf->setOption('margin-top', 40);
            } else {
                $pdf->setOption('margin-top', 85);
            }


            if ($folder_name == 'Twp' && $current_folder_name != 'Henglai') {
                $pdf->setOption('margin-bottom', 40);
                $pdf->setOption('header-html', $headerHtml);
                $pdf->setOption('footer-html', $footerHtml);
                // $pdf->addPage('pdf.Common.lastPageComponent',[
                //     'terms' => isset($quotationLists->terms) ? $quotationLists->terms : null
                // ]);
            } else if ($folder_name == 'Xcepcion') {
                $headerXceHtml = view('pdf.Common.xcepcionHeader', $headerFooterData)->render();
                $footerXceHtml = view('pdf.Common.xcepcionFooter', $headerFooterData)->render();
                $pdf->setOption('margin-bottom', 10);
                $pdf->setOption('header-html', $headerXceHtml);
                $pdf->setOption('footer-html', $footerXceHtml);
            } else if ($folder_name == 'Metis') {
                $metisFooterData = [
                    'quotationData' => $headerFooterData
                ];
                $headerMetHtml = view('pdf.Common.metisHeader', $headerFooterData)->render();
                $footerMetHtml = view('pdf.Common.metisFooter', $metisFooterData)->render();
                $pdf->setOption('margin-bottom', 40);
                $pdf->setOption('margin-left', 15);
                $pdf->setOption('margin-right', 15);
                $pdf->setOption('header-html', $headerMetHtml);
                $pdf->setOption('footer-html', $footerMetHtml);
            } else if ($folder_name == 'Artdecor') {
                $pdf->setOption('margin-bottom', 6);
                $footerArtHtml = view('pdf.Common.artdecorFooter')->render();
                $pdf->setOption('footer-html', $footerArtHtml);
            } else if ($folder_name == 'BuildSpec') {
                $headerBSHtml = view('pdf.Common.buildspecHeader', $headerFooterData)->render();
                $footerBSHtml = view('pdf.Common.buildspecFooter', $headerFooterData)->render();
                $pdf->setOption('margin-bottom', 40);
                $pdf->setOption('header-html', $headerBSHtml);
                $pdf->setOption('footer-html', $footerBSHtml);
            } else if ($folder_name == 'Luxcraft') {
                $metisFooterData = [
                    'quotationData' => $headerFooterData
                ];
                $headerMetHtml = view('pdf.Common.luxcraftHeader', $headerFooterData)->render();
                $footerMetHtml = view('pdf.Common.luxcraftFooter', $metisFooterData)->render();
                $pdf->setOption('margin-bottom', 40);
                $pdf->setOption('margin-left', 15);
                $pdf->setOption('margin-right', 15);
                $pdf->setOption('header-html', $headerMetHtml);
                $pdf->setOption('footer-html', $footerMetHtml);
            } else if ($folder_name == 'Makegood') {
                $pdf->setOption('header-html', "");
                $pdf->setOption('margin-left', 5);
                $pdf->setOption('margin-right', 5);
            } else if ($folder_name == 'Amp') {
                $ampFooterData = [
                    'quotationData' => $headerFooterData
                ];
                $headerAmpHtml = view('pdf.Common.Amp.header', $headerFooterData)->render();
                $footerAmpHtml = view('pdf.Common.Amp.footer', $ampFooterData)->render();
                $pdf->setOption('margin-bottom', 60);
                $pdf->setOption('margin-left', 15);
                $pdf->setOption('margin-right', 15);
                $pdf->setOption('header-html', $headerAmpHtml);
                $pdf->setOption('footer-html', $footerAmpHtml);
            } else if ($folder_name == 'Tag') {
                $pdf->setOption('margin-bottom', 6);
                $headerTagHtml = view('pdf.Common.Tag.header', $headerFooterData)->render();
                $footerTagHtml = view('pdf.Common.Tag.footer')->render();
                $pdf->setOption('footer-html', $footerTagHtml);
                $pdf->setOption('header-html', $headerTagHtml);
            } else if ($folder_name == 'Flynn') {
                $flynnFooterData = [
                    'quotationData' => $headerFooterData
                ];
                $pdf->setOption('margin-bottom', 35);
                $headerFlynnHtml = view('pdf.Common.Flynn.header', $headerFooterData)->render();
                $footerFlynnHtml = view('pdf.Common.Flynn.footer', $flynnFooterData)->render();
                $pdf->setOption('footer-html', $footerFlynnHtml);
                $pdf->setOption('header-html', $headerFlynnHtml);
            } else if ($folder_name == 'Brightway') {
                $pdf->setOption('margin-bottom', 5);
                $pdf->setOption('footer-html', '');
                $pdf->setOption('header-html', '');
            } else if ($folder_name == 'Supaspace') {
                $supaspaceFooterData = [
                    'quotationData' => $headerFooterData
                ];
                $pdf->setOption('margin-bottom', 24);
                $pdf->setOption('margin-left', 0);
                $pdf->setOption('margin-right', 0);
                $headerSupaspaceHtml = view('pdf.Common.Supaspace.header', $headerFooterData)->render();
                $footerSupaspaceHtml = view('pdf.Common.Supaspace.footer', $supaspaceFooterData)->render();
                $pdf->setOption('footer-html', $footerSupaspaceHtml);
                $pdf->setOption('header-html', $headerSupaspaceHtml);
            } else if ($folder_name == 'Tidplus') {
                $pdf->setOption('margin-bottom', 29);
                $pdf->setOption('margin-left', 0);
                $pdf->setOption('margin-right', 0);
                // $pdf->setOption('footer-html', '');
                // $pdf->setOption('header-html', '');
                $headerTidplusHtml = view('pdf.Common.Tidplus.header', $headerFooterData)->render();
                $footerTidplusHtml = view('pdf.Common.Tidplus.footer', $headerFooterData)->render();
                if($printable == 'true'){
                    $pdf->setOption('header-html', '');
                    $pdf->setOption('header-html', '');
                } else {
                    $pdf->setOption('footer-html', $footerTidplusHtml);
                    $pdf->setOption('header-html', $headerTidplusHtml);
                }
            } else if ($folder_name == 'Whst') {
                $pdf->setOption('margin-bottom', 18);
                $headerWhstHtml = view('pdf.Common.Whst.header', $headerFooterData)->render();
                $footerWhstHtml = view('pdf.Common.Whst.footer', $headerFooterData)->render();
                $pdf->setOption('margin-left', 15);
                $pdf->setOption('margin-right', 15);
                $pdf->setOption('footer-html', $footerWhstHtml);
                $pdf->setOption('header-html', $headerWhstHtml);
            } else if ($folder_name == 'Optimum') {
                $optimumFooterData = [
                    'quotationData' => $headerFooterData
                ];
                $pdf->setOption('margin-bottom', 50);
                $pdf->setOption('margin-left', 5);
                $pdf->setOption('margin-right', 5);

                $headerOptimumHtml = view('pdf.Common.Optimum.header', $headerFooterData)->render();
                $footerOptimumHtml = view('pdf.Common.Optimum.footer', $optimumFooterData)->render();
                $pdf->setOption('footer-html', $footerOptimumHtml);
                $pdf->setOption('header-html', $headerOptimumHtml);
            } else if ($folder_name == 'Blackalogy') {

                $blackalogyHeaderFooterData = [
                    'quotationData' => $headerFooterData
                ];

                $pdf->setOption('margin-bottom', 95);
                $pdf->setOption('margin-left', 5);
                $pdf->setOption('margin-right', 5);

                $headerBlackalogyHtml = view('pdf.Common.Blackalogy.header', $blackalogyHeaderFooterData)->render();
                $footerBlackalogyHtml = view('pdf.Common.Blackalogy.footer', $blackalogyHeaderFooterData)->render();
                $pdf->setOption('footer-html', $footerBlackalogyHtml);
                $pdf->setOption('header-html', $headerBlackalogyHtml);

            } else if($folder_name == "Dorlich") {
                $pdf->setOption('margin-bottom', 35);
                $pdf->setOption('margin-left', 15);
                $pdf->setOption('margin-right', 15);

                $headerDorlichHtml = view('pdf.Common.Dorlich.header', $headerFooterData)->render();
                $footerDorlichHtml = view('pdf.Common.Dorlich.footer', $headerFooterData)->render();
                $pdf->setOption('footer-html', $footerDorlichHtml);
                $pdf->setOption('header-html', $headerDorlichHtml);
            } else if ($folder_name == "SaltRoom") {

                $saltRoomHeaderFooterData = [
                    'quotationData' => $headerFooterData
                ];

                $footerSaltRoomHtml = view('pdf.Common.SaltRoom.footer', $saltRoomHeaderFooterData)->render();

                $pdf->setOption('margin-bottom', 10);
                $pdf->setOption('footer-html', $footerSaltRoomHtml);
                $pdf->setOption('header-html', '');

            } else if($folder_name == "Praxis") {
                $pdf->setOption('header-html', '');
                $pdf->setOption('header-html', '');
                $pdf->setOption('margin-bottom', 15);
                $pdf->setOption('margin-left', 17);
                $pdf->setOption('margin-right', 17);

            } else if ($folder_name == 'Aplus') {
                $pdf->setOption('header-html', '');
                $pdf->setOption('footer-html', '');
                $pdf->setOption('margin-top', 10);
                $pdf->setOption('margin-bottom', 10);
                $pdf->setOption('margin-left', 6);
                $pdf->setOption('margin-right', 6);
            } else if ($folder_name == 'Intheory') {
                $pdf->setOption('margin-top', 40);
                $pdf->setOption('margin-bottom', 10);
                $pdf->setOption('margin-left', 15);
                $pdf->setOption('margin-right', 15);

                $test = [
                    'quotationData' => $headerFooterData
                ];
                $headerIntheoryHtml = view('pdf.Common.Intheory.header', $test)->render();
                $pdf->setOption('header-html', $headerIntheoryHtml);
            } else if ($folder_name == 'AcCarpentry') {
                $pdf->setOption('margin-bottom', 29);
                $pdf->setOption('margin-left', 0);
                $pdf->setOption('margin-right', 0);
                $headerTidplusHtml = view('pdf.Common.header', $headerFooterData)->render();
                $footerTidplusHtml = view('pdf.Common.footer', $headerFooterData)->render();
                $pdf->setOption('footer-html', $footerTidplusHtml);
                $pdf->setOption('header-html', $headerTidplusHtml);
            } else if ($folder_name == 'Twp' && $current_folder_name == 'Henglai') {
                $headerHtml = view('pdf.Common.Henglai.header', $headerFooterData)->render();
                $pdf->setOption('header-html', $headerHtml);
            } else if ($folder_name === 'Ideology') {
                $pdf->setOption('margin-bottom', 10);
                $pdf->setOption('margin-left', 15);
                $pdf->setOption('margin-right', 15);
                $headerHtml = view('pdf.Common.Ideology.header', $headerFooterData)->render();
                $footerHtml = view('pdf.Common.Ideology.footer')->render();
                $pdf->setOption('header-html', $headerHtml);
                $pdf->setOption('footer-html', $footerHtml);
            }
            else {
                $pdf->setOption('header-html', $headerHtml);
            }

            $pdfDocument = RenovationDocumentsEloquentModel::find($id);
            switch ($doc_type) {
                case 'QUOTATION':
                    $fileName = 'quotation_' . time() . '.pdf';
                    break;
                case 'VARIATIONORDER':
                    $fileName = 'variation_' . time() . '.pdf';
                    break;
                case 'CANCELLATION':
                    $fileName = 'cancellation_' . time() . '.pdf';
                    break;
                case 'FOC':
                    $fileName = 'foc_' . time() . '.pdf';
                    break;
                default:
                    $fileName = 'quotation_' . time() . '.pdf';
                    break;
            }

            $termsContent = strip_tags($quotationLists->terms);
            if (($current_folder_name == 'Twp' || $current_folder_name == 'Jream' || $current_folder_name == 'Henglai' || $current_folder_name == 'Ideology') && !empty(trim($termsContent))) {
                try {
                    // Generate the first PDF
                    $pdf1Path = storage_path('app/temp_pdf1.pdf');
                    $pdf->save($pdf1Path);

                    // Generate the second PDF
                    $pdf2 = \PDF::loadView('pdf.Common.lastPageComponent', [
                        'current_folder_name' => $current_folder_name,
                        'terms' => $quotationLists->terms,
                        'quotationData' => $headerFooterData,
                        'customers_array' => $customers_array,
                        'company_logo' => $companies['company_logo'],
                        'settings' => [
                            'enable_only_show_discount_amount' => $enable_only_show_discount_amount->value,
                            'enable_show_last_name_first' => $enable_show_last_name_first->value
                        ]
                    ]);

                    $pdf2Path = storage_path('app/temp_pdf2.pdf');
                    $pdf2->setOption('margin-bottom', 20);
                    $pdf2->setOption('margin-top', 20);
                    $pdf2->setOption('header-html', "");
                    $pdf2->setOption('footer-html', "");
                    $pdf2->save($pdf2Path);

                    return $this->mergePdf($pdf1Path, $pdf2Path, $pdfDocument);
                } catch (\Exception $ex) {
                    // Check and delete temp files if they exist
                    if (file_exists(storage_path('app/temp_pdf1.pdf'))) {
                        unlink(storage_path('app/temp_pdf1.pdf'));
                    }
                    if (file_exists(storage_path('app/temp_pdf2.pdf'))) {
                        unlink(storage_path('app/temp_pdf2.pdf'));
                    }

                    // Rethrow the exception if you want itF to be handled further up the stack
                    return $ex;
                }
            } else {

                $fileName = 'quotation_' . time() . '.pdf';
                // Save the PDF to a file in the 'public' disk
                $filePath = 'pdfs/' . $fileName;
                Storage::disk('public')->put($filePath, $pdf->output());

                // Store the file path in the database (assuming you have a model PdfDocument)
                if ($pdfDocument) {
                    // Check if the old PDF file exists and delete it
                    // Update the database with the new file name
                    if($printable === 'true'){

                        if (!empty($pdfDocument->printable_pdf) && Storage::disk('public')->exists('pdfs/' . $pdfDocument->printable_pdf)) {
                            Storage::disk('public')->delete('pdfs/' . $pdfDocument->printable_pdf);
                        }

                        $pdfDocument->update([
                            'printable_pdf' => $fileName
                        ]);
                    } else {

                        if (!empty($pdfDocument->pdf_file) && Storage::disk('public')->exists('pdfs/' . $pdfDocument->pdf_file)) {
                            Storage::disk('public')->delete('pdfs/' . $pdfDocument->pdf_file);
                        }

                        $pdfDocument->update([
                            'pdf_file' => $fileName
                        ]);
                    }
                }
                if (!$request->pdf_status && $request->pdf_status != 'SAVE') {
                    return $pdf->download("Quotation version" . $quotationLists->version_num . ".pdf");
                }
            }
        } catch (\Exception $th) {
            // Log::channel('daily')->error($th);
            return $th;
        }
    }

    // NOTE: Not in a proper DDD way
    public function generateRenovationDocumentAgrNo(Request $request)
    {
        // If renovation_document_id is passed from frontend (editing renovation documents), return the saved agreement_no
        if ($request->filled('renovation_document_id')) {
            $renoDoc = RenovationDocumentsEloquentModel::find($request->renovation_document_id);

            if (isset($renoDoc)) {
                if (!empty($renoDoc->agreement_no)){
                    $project = ProjectEloquentModel::find($request->project_id);
                    $result = $renoDoc->agreement_no;
                    if($project->project_status == 'InProgress' && $request->document_type == 'QO'){
                        $result = preg_replace('/\/QO(?:[-\/]?\d+)?$/', '', $renoDoc->agreement_no);
                    }
                    return response()->success($result, "Success", Response::HTTP_OK);
                }
                else {
                    $project = ProjectEloquentModel::find($request->project_id);

                    if (isset($project)) {
                        $documentType = $request->document_type ?? '';

                        $agreementNum = $project->agreement_no . '/' . $documentType;

                        return response()->success($agreementNum, "Success", Response::HTTP_OK);
                    }
                }
            }
        } else { // If renovation_document_id is not passed from frontend (creating new renovation documents), generate an agreement_no and return to frontend
            $project = ProjectEloquentModel::find($request->project_id);

            if (isset($project)) {
                $name = auth('sanctum')->user()->first_name . ' ' . auth('sanctum')->user()->last_name;
                $initialSalespersonName = implode("", array_map(fn($word) => $word[0], explode(" ", $name)));
                $agreementDate = isset($project->created_at) ? Carbon::parse($project->created_at)->toDateString() : Carbon::now()->toDateString();

                $documentType = $request->document_type ?? '';

                $documentCount = 0;

                if ($documentType != '' || $documentType != 'QO') {
                    $documentCount = RenovationDocumentsEloquentModel::where('project_id', $request->project_id)->where('type', $documentType)->count();
                }

                $common_quotation_running_number = 0;
                $common_project_running_number = 0;
                $checkCommonProjectNumSetting = GeneralSettingEloquentModel::where('setting', "enable_common_project_running_number")
                ->where('value', "true")
                ->first();
                $commonPjNum = GeneralSettingEloquentModel::where('setting','common_project_start_number')->first();
                $commonQONum = GeneralSettingEloquentModel::where('setting','common_quotation_start_number')->first();

                $longDocumentType = $documentType == 'VO' ? 'VARIATIONORDER' : ($documentType == 'CN' ? 'CANCELLATION' : 'FOC');
                $versionCount =  RenovationDocumentsEloquentModel::where('project_id', $request->project_id)->where('type', $longDocumentType)->count();
                if($checkCommonProjectNumSetting){
                    $common_project_running_number = $commonPjNum->value;
                    $common_quotation_running_number = $commonQONum->value;
                    $agreementNum = generateAgreementNumber('renovation_document', [
                        'company_initial' => $project->company->docu_prefix,
                        'quotation_initial' => $project->company->quotation_prefix,
                        'salesperson_initial' => $initialSalespersonName,
                        'block_num' => $project->property->block_num ?? null,
                        'date' => $agreementDate,
                        'document_type' => $documentType . ($documentCount > 0 ? ($documentCount++) : ''),
                        'running_num' => $project->company->invoice_no_start,
                        'version_num' => $documentType == 'QO' ? '1' : $versionCount + 1,
                        'project_id' => $project->id,
                        'quotation_num' => $project->company->quotation_no,
                        'project_agr_no' => $project->agreement_no,
                        'common_project_running_number' => $common_project_running_number,
                        'common_quotation_running_number' => $common_quotation_running_number
                    ]);

                }else{

                    $agreementNum = generateAgreementNumber('renovation_document', [
                        'company_initial' => $project->company->docu_prefix,
                        'quotation_initial' => $project->company->quotation_prefix,
                        'salesperson_initial' => $initialSalespersonName,
                        'block_num' => $project->property->block_num ?? null,
                        'date' => $agreementDate,
                        'document_type' => $documentType . ($documentCount > 0 ? ($documentCount++) : ''),
                        'running_num' => $project->company->invoice_no_start,
                        'version_num' => $documentType == 'QO' ? '1' : $versionCount + 1,
                        'project_id' => $project->id,
                        'quotation_num' => $project->company->quotation_no,
                        'project_agr_no' => $project->agreement_no,
                        'common_project_running_number' => $common_project_running_number,
                        'common_quotation_running_number' => $common_quotation_running_number
                    ]);
                }

                return response()->success($agreementNum, "Success", Response::HTTP_OK);
            }
        }
    }

    public function updateDocumentStatus(RenovationDocumentsEloquentModel $renovation_document,$status)
    {
        $renovation_document->update(['status' => $status]);
        $userId = auth()->user()->id;
        $renovation_document->approvers()->attach($userId);
        return response()->success('Successfully Updated', "Success", Response::HTTP_OK);
    }

    public function uploadSignedDocument(StoreSingedDocumentUploadRequest $request,RenovationDocumentsEloquentModel $renovation_documents,$document_type)
    {
        try {

            DB::beginTransaction();
            $this->sign($request);
            $renovation_document = RenovationDocumentsEloquentModel::find($request->id);
            if($renovation_document){
                if (request()->hasFile('document_file') && request()->file('document_file')->isValid()) {
                    $renovation_document->clearMediaCollection('document_file');
                    $renovation_document->addMediaFromRequest('document_file')->toMediaCollection('document_file', 'signed_documents');
                }
            }

            DB::commit();
            return response()->success($renovation_document, 'success', Response::HTTP_OK);
        } catch (\Exception $error) {
            DB::rollBack();
            return response()->error($error->getMessage(), 'error', Response::HTTP_EXPECTATION_FAILED);
        }
    }

    public function updateQuotationDetail($document_id, Request $request)
    {
        try {

            DB::beginTransaction();
            $data = $request->all();
           $document = (new UpdateQuotationDetailCommand($document_id, $data))->execute();

           $document = RenovationDocumentsEloquentModel::find($document_id);
           if($document){
               $request['renovation_document_id'] = $document->id;
               $request['project_id'] = $document->project_id;
               $request['printable'] = "false";
               $request['type'] = "QUOTATION";
               $this->downloadPdf($request);
           }
            DB::commit();
            return response()->success($document, 'success', Response::HTTP_OK);
        } catch (\Exception $error) {
            DB::rollBack();
            return response()->error($error->getMessage(), 'error', Response::HTTP_EXPECTATION_FAILED);
        }
    }
}
