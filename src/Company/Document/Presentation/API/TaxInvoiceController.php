<?php

namespace Src\Company\Document\Presentation\API;

use DateTime;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\Document\Application\UseCases\Commands\SignTaxByManagerCommand;
use Src\Company\Document\Application\UseCases\Commands\SignTaxBySaleCommand;
use Src\Company\Document\Application\UseCases\Queries\FindTaxByIdQuery;
use Src\Company\Document\Application\UseCases\Queries\GetTaxInvoicesByStatusOrder;
use Src\Company\Document\Infrastructure\EloquentModels\TaxInvoiceEloquentModel;
use Symfony\Component\HttpFoundation\Response;
use Src\Company\Document\Application\Policies\TaxInvoicePolicy;
use Src\Company\Document\Application\UseCases\Commands\ChangeTaxInvoiceStatusCommand;
use Src\Company\Project\Application\UseCases\Queries\FindProjectDetailForHandoverQuery;
use Src\Company\Project\Domain\Resources\ProjectDetailForHandoverResource;
use Src\Company\Project\Infrastructure\EloquentModels\ProjectEloquentModel;
use Src\Company\System\Application\UseCases\Queries\FindGeneralSettingByNameQuery;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;

class TaxInvoiceController extends Controller
{
    public function index($id): JsonResponse
    {
        // abort_if(authorize('view', TaxInvoicePolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Statement of Account!');

        try {
            $final_result = TaxInvoiceEloquentModel::where('project_id', $id)->get(['id', 'created_at', 'status']);

            return response()->json($final_result, Response::HTTP_CREATED);
        } catch (\DomainException $domainException) {

            return response()->json(['error' => $domainException->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function show($id): JsonResponse
    {
        // abort_if(authorize('view', TaxInvoicePolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Statement of Account!');

        try {

            $final_result = (new FindTaxByIdQuery($id))->handle();

            return response()->json($final_result, Response::HTTP_CREATED);
        } catch (\DomainException $domainException) {

            return response()->json(['error' => $domainException->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function getByStatusOrder(Request $request): JsonResponse
    {
        abort_if(authorize('view', TaxInvoicePolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Statement of Account!');

        try {

            $filters = $request->all();

            $taxInvoices = (new GetTaxInvoicesByStatusOrder($filters))->handle();

            return response()->success($taxInvoices, 'Statement of Account lists ordered by pending,approved', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function TaxInvoiceSignBySaleperson(Request $request): JsonResponse
    {
        abort_if(authorize('sign_by_salesperson', TaxInvoicePolicy::class), Response::HTTP_FORBIDDEN, 'Need sign_by_salesperson permission for Statement of Account!');

        try {
            (new SignTaxBySaleCommand($request))->execute();

            return response()->json('success sign', Response::HTTP_CREATED);
        } catch (\DomainException $domainException) {

            return response()->json(['error' => $domainException->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function TaxInvoiceSignByManager(Request $request): JsonResponse
    {
        // abort_if(authorize('sign_by_manager', TaxInvoicePolicy::class), Response::HTTP_FORBIDDEN, 'Need sign_by_manager permission for Statement of Account!');

        try {
            (new SignTaxByManagerCommand($request))->execute();

            return response()->json('success sign by manager', Response::HTTP_CREATED);
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

    function changeFormatDate($dateString)
    {
        try {
            $date = new DateTime($dateString);
            return $date->format('d/m/Y');
        } catch (\Exception $e) {
            // Handle exception if the date cannot be parsed
            return null;
        }
    }
    public function taxInvoicedownloadPdf(Request $request)
    {
        $projectId = $request->projectId;
        $project = ProjectEloquentModel::query()
            ->with([
                'saleReport.customer_payments',
                'company',
                'property',
                'renovation_documents' => function ($query) {
                    $query->whereNotNull('signed_date');
                }
            ])
            ->findOrFail($projectId);
        // $taxInvoice = (new FindTaxByIdQuery($request->taxId))->handle();
        $taxInvoice = TaxInvoiceEloquentModel::with('salesperson')->find($request->taxId);
        $salePerson = UserEloquentModel::with('staffs')->where('id', $taxInvoice->signed_by_saleperson_id)->first();
        $manager = UserEloquentModel::where('id', $taxInvoice->signed_by_manager_id)->first(['first_name', 'last_name']);
        $folder_name  = config('folder.company_folder_name') == 'Magnum' ? 'Twp' : config('folder.company_folder_name');

        $customers_array = $project->customersPivot->toArray();
        $enable_show_last_name_first = (new FindGeneralSettingByNameQuery('enable_show_last_name_first'))->handle();


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
            "tel" => $project->company->tel,
            "fax" => $project->company->fax
        ];

        $headerData = [
            'project' => $project,
            'our_ref' => $project->agreement_no,
            'agr' => $project->agreement_no,
            'properties' => $properties,
            'companies' => $companies,
            'folder_name' => $folder_name,
            'customers' => $customers,
            'customers_array' => $customers_array,
            'signed_sale_email' => $taxInvoice->salesperson->email,
            'signed_sale_ph' => $taxInvoice->salesperson->contact_no,
            'signed_saleperson' => $taxInvoice->salesperson->first_name . ' ' . $taxInvoice->salesperson->last_name,
            'signed_date' => $taxInvoice->date ? $this->changeFormatDate($taxInvoice->date) : null,
            'created_at' => $taxInvoice->created_at ? $this->changeFormatDate($taxInvoice->created_at) : null,
            'enable_show_last_name_first' => $enable_show_last_name_first->value
        ];

        $saleperson_signature = null;
        $manager_signature = null;
        if ($taxInvoice->salesperson_signature) {
            $saleperson_signature_file_path = 'tax-invoice/saleperson/sign/' . $taxInvoice->salesperson_signature;

            $saleperson_signature_image = Storage::disk('public')->get($saleperson_signature_file_path);

            $saleperson_signature = base64_encode($saleperson_signature_image);
        }

        if ($taxInvoice->manager_signature) {
            $manager_signature_file_path = 'tax-invoice/manager/sign/' . $taxInvoice->manager_signature;

            $manager_signature_image = Storage::disk('public')->get($manager_signature_file_path);

            $manager_signature = base64_encode($manager_signature_image);
        }

        $data = [
            'project' => $project,
            'tax' => $taxInvoice,
            'saleperson' => $salePerson,
            'manager' => $manager,
            'saleperson_signature' => $saleperson_signature,
            'manager_signature' => $manager_signature,
            'is_magnum' => config('folder.company_folder_name') == 'Magnum' ? true : false,
            'is_artdecor' => config('folder.company_folder_name') == 'Artdecor' ? true : false,
            'quotationData' => $headerData
        ];

        $headerHtml = view('pdf.Common.taxHeader', $headerData)->render();
        $pdf = \PDF::loadView('pdf.TaxInvoice.taxInvoice', $data);
        $pdf->setOption('enable-javascript', true);
        if ($folder_name == 'Miracle') {
            $pdf->setOption('margin-top', 90);
            $pdf->setOption('header-html', $headerHtml);
        } else if ($folder_name == 'Artdecor') {
            $pdf->setOption('margin-top', 10);
        } else {
            $pdf->setOption('margin-top', 90);
            $pdf->setOption('header-html', $headerHtml);
        }
        return $pdf->download("Statement of Account.pdf");
    }

    public function changeTaxInvoiceStatus(Request $request, $id)
    {
        try {
            $status = $request->status;
            (new ChangeTaxInvoiceStatusCommand($id, $status))->execute();

            return response()->json('success change status', Response::HTTP_CREATED);
        } catch (\DomainException $domainException) {

            return response()->json(['error' => $domainException->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }
}
