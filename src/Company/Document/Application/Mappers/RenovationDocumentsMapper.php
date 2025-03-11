<?php

namespace Src\Company\Document\Application\Mappers;

use Illuminate\Http\Request;
use Src\Company\Document\Domain\Model\Entities\RenovationDocuments;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Src\Company\Document\Application\DTO\RenovationDocumentData;
use Src\Company\Document\Infrastructure\EloquentModels\RenovationDocumentsEloquentModel;
use Carbon\Carbon;
use Src\Company\Document\Infrastructure\EloquentModels\RenovationSettingEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\ProjectEloquentModel;
use Src\Company\System\Infrastructure\EloquentModels\CompanyEloquentModel;
use Src\Company\System\Infrastructure\EloquentModels\GeneralSettingEloquentModel;
use Illuminate\Support\Facades\Log;

class RenovationDocumentsMapper {

    public static function fromRequest(Request $request, ?int $renovation_document_id = null): RenovationDocuments
    {
        $salespersonSignatureFile = $customerSignatureFile = '';

        if($request->file('salesperson_signature'))
        {
            $salesFileName =  time().'.'.$request->file('salesperson_signature')->extension();

            $salesFilePath = 'renovation_document/salesperson_signature_file/' . $salesFileName;

            Storage::disk('public')->put($salesFilePath, file_get_contents($request->file('salesperson_signature')));

            $salespersonSignatureFile = $salesFileName;
        }

        if($request->file('customer_signature'))
        {
            $customerFileName =  time().'.'.$request->file('customer_signature')->extension();

            $customerFilePath = 'customer_signature_file/' . $customerFileName;

            Storage::disk('public')->put($customerFilePath, file_get_contents($request->file('customer_signature')));

            $customerSignatureFile = $customerFileName;
        }

        //get authenticated salesperson id
        $salespersonId = auth('sanctum')->user()->staffs->id;

        return new RenovationDocuments(
            id: $renovation_document_id,
            type: $request->string('type'),
            version_number: $request->string('version_number'),
            total_amount: $request->float('total_amount'),
            disclaimer: $request->string('disclaimer'),
            special_discount_percentage: $request->string('special_discount_percentage'),
            additional_notes: $request->string('additional_notes'),
            salesperson_signature: $salespersonSignatureFile,
            signed_by_salesperson_id: $salespersonId,
            customer_signature: $customerSignatureFile,
            project_id: $request->integer('project_id'),
            document_standard_id: $request->document_standard_id,
            ismerged: $request->isSignedAccepted,
            payment_terms: $request->payment_terms,
            agreement_number: $request->string('agreement_no'),
            remark: $request->string('remark')
        );
    }

    public static function toEloquent(RenovationDocuments $document): RenovationDocumentsEloquentModel
    {
        $documentNumber = 0;

        if($document->type == 'QUOTATION')
        {

            $versionNumber = RenovationDocumentsEloquentModel::find($document->id)->version_number ?? 1;


            $count =  RenovationDocumentsEloquentModel::where([
                ['project_id', $document->project_id],
                ['type', $document->type]
            ])->count();

            $versionNumberCount = isset($versionNumber) ? (int) $versionNumber : 1;

            /**
             * it won't create new version if sale_person signature is null as discuss with D
             */

            $isSignedAlready = RenovationDocumentsEloquentModel::find($document->id,['salesperson_signature'])->salesperson_signature  ?? null;


            if($count==0){
                $versionNumberCount = 1;
            }

            if(!empty($isSignedAlready))
            {

                $versionNumber = RenovationDocumentsEloquentModel::where([
                    ['project_id', $document->project_id],
                    ['type', $document->type]
                ])
                ->orderByRaw("CAST(version_number AS SIGNED) DESC")
                ->pluck('version_number')
                ->first();


                $versionNumberCount = (int) $versionNumber + 1;



            }
            else if($document->type == 'QUOTATION' && empty($isSignedAlready) && $count !== 0)
            {
                $versionNumberCount = RenovationDocumentsEloquentModel::find($document->id,['version_number'])->version_number;


                RenovationDocumentsEloquentModel::where('id',$document->id)->forceDelete();

                RenovationSettingEloquentModel::where([
                    'renovation_document_id' => $document->id,
                    'setting' => 'hide_total_in_print'
                ])->forceDelete();
            }
        }
        else {
            $documentNumber = RenovationDocumentsEloquentModel::where('project_id', $document->project_id)->whereNotNull('signed_date')->where('type', $document->type)->count();
            $documentNumber++;
        }

        $versionNumber = $document->type == 'QUOTATION' ? $versionNumberCount : ''; // if type not quotation ver num will be null
        $documentEloquent = new RenovationDocumentsEloquentModel();

        $documentEloquent->total_amount = $document->total_amount;

        // Agreement Number
        $project = ProjectEloquentModel::findOrFail($document->project_id);
        $company = CompanyEloquentModel::findOrFail($project->company_id, ['docu_prefix', 'invoice_no_start', 'quotation_no', 'quotation_prefix']);
        $initialCompanyName = $company->docu_prefix;
        $quotationPrefix = $company->quotation_prefix;

        $name = auth('sanctum')->user()->first_name . ' ' . auth('sanctum')->user()->last_name;
        $initialSalespersonName = implode("", array_map(fn ($word) => $word[0], explode(" ", $name)));

        switch ($document->type) {
            case 'QUOTATION':
                $documentTypeShortName = "QO";
                break;

            case 'VARIATIONORDER':
                $documentTypeShortName = "VO";
                break;

            case 'FOC':
                $documentTypeShortName = "FOC";
                break;

            case 'CANCELLATION':
                $documentTypeShortName = "CN";
                break;

            default:
                $documentTypeShortName = '';
                break;
        }

        $common_quotation_running_number = 0;
        $common_project_running_number = 0;
        $agreementDate = isset($project->created_at) ? Carbon::parse($project->created_at)->toDateString() : Carbon::now()->toDateString();
        $commonPjNum = GeneralSettingEloquentModel::where('setting','common_project_start_number')->first();
        $commonQONum = GeneralSettingEloquentModel::where('setting','common_quotation_start_number')->first();
        $common_project_running_number = $commonPjNum->value ?? 0;
        $common_quotation_running_number = $commonQONum->value ?? 0;
        $enableDataMigrationMode = GeneralSettingEloquentModel::where('setting', "enable_data_migration_mode")
        ->where('value', "true")
        ->first();
        if ($enableDataMigrationMode) {
            $agreementNum = $document->agreement_number ?? '';
        } else {
            $agreementNum = generateAgreementNumber('renovation_document', [
                'company_initial' => $initialCompanyName,
                'quotation_initial' => $quotationPrefix,
                'salesperson_initial' => $initialSalespersonName,
                'block_num' => $project->property->block_num ?? null,
                'date' => $agreementDate,
                'document_type' => $documentTypeShortName . ($documentNumber > 0 ? ($documentNumber) : ''),
                'running_num' => $company->invoice_no_start,
                'version_num' => $versionNumber,
                'project_id' => $project->id,
                'quotation_num' => $company->quotation_no,
                'project_agr_no' => $project->agreement_no,
                'common_project_running_number' => $common_project_running_number,
                'common_quotation_running_number' => $common_quotation_running_number
            ]);
        }

        if ($document->id && $document->type !== 'QUOTATION') {
            $documentEloquent = RenovationDocumentsEloquentModel::query()->findOrFail($document->id);

            //trce total amount issues
            if($documentEloquent->type == 'VARIATIONORDER')
            {
                $documentEloquent->total_amount = $document->total_amount;
            }else if($documentEloquent->type == 'CANCELLATION'){
                // $documentEloquent->total_amount = $document->total_amount + $documentEloquent->total_amount;
                $documentEloquent->total_amount = $document->total_amount;
            }
        }

        /***
         * check if the setting on the role is allow exist or not if exist it will enable pending and reject feature
         *
         */
        // $isAllowApproveSetting = GeneralSettingEloquentModel::where('setting','reno_approve_setting')->first()->value ?? null;
        // $isAllowApproveAndPendingFeature = GeneralSettingEloquentModel::where('setting','quotation_approval_roles')->first()->value ?? null;
        // $initialAffectRenoDocumentForApprove = GeneralSettingEloquentModel::where('setting','reno_approval_affect_items')->first()->value ?? null;
        // Assuming $tempValues is an associative array
        // $affectRenoDocumentForApprove = isset($initialAffectRenoDocumentForApprove) 
        // ? explode(',', $initialAffectRenoDocumentForApprove) 
        // : [];

        // // checking whether login role is Management or not
        // $userRole = auth()->user()->roles;
        // $nameToCheck = "Management";
        // $isManagement = collect($userRole)->contains('name', $nameToCheck);

        // if($isAllowApproveSetting == 'true')
        // {

        //     Log::channel('daily')->info($isAllowApproveSetting);

        //     if(!$isManagement && !empty($isAllowApproveAndPendingFeature) && !empty($affectRenoDocumentForApprove) && in_array($document->type,$affectRenoDocumentForApprove))
        //     {
        //         $documentEloquent->status = 'pending';
        //     } 
        // }
        $isAllowApproveSetting = GeneralSettingEloquentModel::where('setting','reno_approve_setting')->where('value','true')->first()->value ?? null;
        $approvalSettings = GeneralSettingEloquentModel::where('setting', 'document_approval_setting_value')->first()->value ?? null;
        $isTurnOffManagerQOApproval = GeneralSettingEloquentModel::where('setting', 'turn_off_manager_auto_approve')->where('value','true')->first()->value ?? null;
        $approvalSettings = $approvalSettings ? json_decode($approvalSettings, true) : [];

        $userRoles = auth()->user()->roles->pluck('name')->toArray();
        // $isManagement = in_array('Management', $userRoles);
        if($isTurnOffManagerQOApproval){
            $isManagementOrManager = in_array('Management', $userRoles);
        }else {
            $isManagementOrManager = in_array('Manager', $userRoles) || in_array('Management', $userRoles);
        }
        $documentConfig = collect($approvalSettings)->firstWhere('document_type', $document->type);

        if ($isAllowApproveSetting && $documentConfig && $documentConfig['is_select']) {
            logger(['dam']);
            Log::channel('daily')->info('Approval process started for document: ' . $document->type);
        
            if ($documentConfig['is_2level']) {
                // Two-level approval: Set status to pending regardless of role
                if(!$isManagementOrManager){
                    $documentEloquent->status = 'pending';
                }
                Log::channel('daily')->info("Document {$document->type} requires two-level approval. Status set to pending.");
            } else {
                // Single-level approval
                $firstApprover = $documentConfig['approvers'][0] ?? null;
        
                if ($firstApprover && !$isManagementOrManager) {
                    $documentEloquent->status = 'pending';
                    Log::channel('daily')->info("Document {$document->type} set to pending for role: {$firstApprover}");
                } else {
                    Log::channel('daily')->info("Document {$document->type} remains approved for Management role.");
                }
            }
        }
        

        $documentEloquent->type = $document->type;
        $documentEloquent->version_number = $versionNumber; // if type not quotation ver num will be null
        $documentEloquent->disclaimer = $document->disclaimer;
        $documentEloquent->special_discount_percentage = $document->special_discount_percentage;
        $documentEloquent->agreement_no = $agreementNum;

        $documentEloquent->signed_date = null;
        $documentEloquent->ismerged = $document->ismerged ?? false;
        $documentEloquent->salesperson_signature = $document->salesperson_signature;
        $documentEloquent->updated_by_user = auth('sanctum')->user()->id;
        $documentEloquent->signed_by_salesperson_id = $document->signed_by_salesperson_id;
        $documentEloquent->additional_notes = $document->additional_notes;
        $documentEloquent->customer_signature = $document->customer_signature;
        $documentEloquent->project_id = $document->project_id;
        $documentEloquent->document_standard_id = $document->document_standard_id;
        $documentEloquent->payment_terms = $document->payment_terms;
        $documentEloquent->remark = $document->remark;

        return $documentEloquent;
    }

    public static function fromEloquent(RenovationDocumentsEloquentModel $documentEloquent): RenovationDocumentData
    {
        return new RenovationDocumentData(
            id: $documentEloquent->id,
            type: $documentEloquent->type,
            version_number: $documentEloquent->version_number,
            total_amount: $documentEloquent->total_amount,
            disclaimer: $documentEloquent->disclaimer,
            special_discount_percentage: $documentEloquent->special_discount_percentage,
            additional_notes: $documentEloquent->additional_notes,
            salesperson_signature: $documentEloquent->salesperson_signature,
            signed_by_salesperson_id: $documentEloquent->signed_by_salesperson_id,
            customer_signature: $documentEloquent->customer_signature,
            project_id: $documentEloquent->project_id,
            document_standard_id: $documentEloquent->document_standard_id,
            payment_terms: $documentEloquent->payment_terms,
            remark: $documentEloquent->remark
        );
    }

}
