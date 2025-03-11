<?php

namespace Src\Common\Presentation\CLI;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Src\Company\Document\Infrastructure\EloquentModels\ContractEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\DocumentEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\EvoEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\EvoItemEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\EvoSignatureEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\FolderEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\HandoverCertificateEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\HandoverCertificateSignatureEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\HDBFormsEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\ProjectRequirementEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\PurchaseOrderEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\PurchaseOrderItemEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\QuotationTemplateItemsEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\RenovationDocumentsEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\RenovationDocumentSignaturesEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\RenovationItemsEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\SectionAreaOfWorkEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\SectionsEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\TaxInvoiceEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\CustomerPaymentEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\ProjectEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\PropertyEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\RenovationItemScheduleEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\SaleReportEloquentModel;
use Src\Company\CustomerManagement\Infrastructure\EloquentModels\CustomerEloquentModel;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;

class CreateTableClearCmd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'table:clear {--project_id=*}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clears data from predefined tables';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Schema::disableForeignKeyConstraints();
        $tablesToClear = Config::get('clearable_tables.tables');
        $projectIds = $this->option('project_id');

        if (!empty($projectIds)) {
            // Handle comma-separated IDs in a single option
            $projectIds = array_filter(array_map('trim', explode(',', implode(',', $projectIds))));

            $userIdCounts = DB::table('customer_project')
                ->select('user_id', DB::raw('COUNT(*) as count'))
                ->groupBy('user_id')
                ->pluck('count', 'user_id');

            $userRawIds = DB::table('customer_project')
                ->select('user_id')
                ->whereIn('project_id', $projectIds)
                ->pluck('user_id');

            $userIds = $this->filterUserIdForMultipleProjects($userRawIds, $userIdCounts);

            $customer_ids = CustomerEloquentModel::whereIn('user_id', $userIds)->pluck('id');

            $property_ids = DB::table('customer_properties')->whereIn('customer_id', $customer_ids)->pluck('id');

            $evo_ids = EvoEloquentModel::whereIn('project_id', $projectIds)->pluck('id');

            $purchase_order_ids = PurchaseOrderEloquentModel::whereIn('project_id', $projectIds)->pluck('id');

            $handover_ids = HandoverCertificateEloquentModel::whereIn('project_id', $projectIds)->pluck('id');

            $document_ids = RenovationDocumentsEloquentModel::whereIn('project_id', $projectIds)->pluck('id');

            $vendor_payment_ids = DB::table('vendor_invoices')->whereIn('purchase_order_id', $projectIds)->pluck('id');

            $this->checkProjectExists($projectIds);
        }

        foreach ($tablesToClear as $table) {
            // Check if table exists to avoid potential errors
            if (Schema::hasTable($table)) {
                if (!empty($projectIds)) {
                    $this->specificProjectClear($table, $projectIds, $customer_ids, $userIds, $property_ids, $evo_ids, $purchase_order_ids, $handover_ids, $document_ids, $vendor_payment_ids);
                } else {
                    $this->defaultClear($table);
                }
            } else {
                $this->error("Table {$table} does not exist.");
            }
        }
        Schema::enableForeignKeyConstraints();
    }

    public function checkProjectExists($projectIds)
    {
        foreach ($projectIds as $projectId) {
            $project = ProjectEloquentModel::find($projectId);
            if (!$project) {
                $this->error("Project Id {$projectId} does not exist");
            } else {
                $this->info("Cleared data from table by project id {$projectId}");
            }
        }
    }

    protected function filterUserIdForMultipleProjects($userRawIds, $userIdCounts)
    {
        // Convert collection to array
        $userIdsArray = $userRawIds->toArray();

        // Filter user IDs that appear exactly once in the whole table
        return array_values(array_filter($userIdsArray, function ($userId) use ($userIdCounts) {
            return $userIdCounts[$userId] === 1;
        }));
    }

    public function defaultClear($table)
    {
        if ($table == 'users') {
            DB::table('role_user')
                ->whereIn('user_id', function ($query) {
                    $query->select('users.id')
                        ->from('users')
                        ->join('customers', 'users.id', '=', 'customers.user_id');
                })
                ->delete();
            DB::table('users')
                ->join('customers', 'users.id', '=', 'customers.user_id')
                ->delete();
            $this->info("Cleared data from {$table} base on customers");
        } else if ($table == 'section_area_of_works' || $table == 'quotation_template_items' || $table == 'sections') {
            DB::table($table)
                ->whereNotNull('document_id')
                ->where('document_id', '<>', 0)
                ->delete();
            $this->info("Cleared data from {$table} where document_id is not 0 and not null");
        } else {
            DB::table($table)->truncate();
            $this->info("Cleared data from {$table}");
        }
    }

    public function specificProjectClear($table, $projectIds, $customer_ids, $userIds, $property_ids, $evo_ids, $purchase_order_ids, $handover_ids, $document_ids, $vendor_payment_ids)
    {
        if ($table == 'users') {
            UserEloquentModel::whereIn('id', $userIds)->forceDelete();
        } else if ($table == 'customer_project') {
            // delete data from table related with project
            DB::table('customer_project')->whereIn('project_id', $projectIds)->delete();
        } else if ($table == 'contracts') {
            ContractEloquentModel::whereIn('project_id', $projectIds)->forceDelete();
        } else if ($table == 'customer_payments') {
            CustomerPaymentEloquentModel::whereIn('customer_id', $customer_ids)->forceDelete();
        } else if ($table == 'customer_properties') {
            DB::table('customer_properties')->whereIn('customer_id', $customer_ids)->delete();
        } else if ($table == 'customers') {
            CustomerEloquentModel::whereIn(
                'id',
                $customer_ids
            )->forceDelete();
        } else if ($table == 'evo_item_rooms') {
            $evo_item_ids = EvoItemEloquentModel::whereIn('evo_id', $evo_ids)->pluck('id');
            DB::table('evo_item_rooms')->whereIn('item_id', $evo_item_ids)->delete();
        } else if ($table == 'evo_items') {
            EvoItemEloquentModel::whereIn('evo_id', $evo_ids)->forceDelete();
        } else if ($table == 'evo_signatures') {
            EvoSignatureEloquentModel::whereIn('evo_id', $evo_ids)->delete();
        } else if ($table == 'evos') {
            EvoEloquentModel::whereIn('id', $evo_ids)->forceDelete();
        } else if ($table == 'folders') {
            FolderEloquentModel::whereIn('project_id', $projectIds)->forceDelete();
        } else if ($table == 'handover_certificate_signatures') {
            HandoverCertificateSignatureEloquentModel::whereIn('handover_certificate_id', $handover_ids)->delete();
        } else if ($table == 'handover_certificates') {
            HandoverCertificateEloquentModel::whereIn('id', $handover_ids)->forceDelete();
        } else if ($table == 'hdb_acknowledgement_forms') {
            HDBFormsEloquentModel::whereIn('project_id', $projectIds)->delete();
        } else if ($table == 'lead_checklist_items') {
            DB::table('lead_checklist_items')->whereIn('customer_id', $customer_ids)->delete();
        } else if ($table == 'project_requirements') {
            ProjectRequirementEloquentModel::whereIn('project_id', $projectIds)->forceDelete();
        } else if ($table == 'properties') {
            PropertyEloquentModel::whereIn('id', $property_ids)->forceDelete();
        } else if ($table == 'purchase_order_items') {
            PurchaseOrderItemEloquentModel::whereIn('purchase_order_id', $purchase_order_ids)->forceDelete();
        } else if ($table == 'purchase_orders') {
            PurchaseOrderEloquentModel::whereIn('id', $purchase_order_ids)->forceDelete();
        } else if ($table == 'renovation_item_sections') {
            DB::table('renovation_item_sections')->whereIn('document_id', $document_ids)->delete();
        } else if ($table == 'renovation_item_area_of_works') {
            logger('dat',[$document_ids]);
            DB::table('renovation_item_area_of_works')->whereIn('document_id', $document_ids)->delete();
        } else if ($table == 'renovation_item_schedules') {
            RenovationItemScheduleEloquentModel::whereIn('project_id', $projectIds)->forceDelete();
        } else if ($table == 'renovation_items') {
            RenovationItemsEloquentModel::whereIn('project_id', $projectIds)->forceDelete();
        } else if ($table == 'renovation_document_signatures') {

            RenovationDocumentSignaturesEloquentModel::whereIn('renovation_document_id', $document_ids)->delete();
        } else if ($table == 'renovation_documents') {
            RenovationDocumentsEloquentModel::whereIn('project_id', $projectIds)->forceDelete();
        } else if ($table == 'sale_reports') {
            SaleReportEloquentModel::whereIn('project_id', $projectIds)->delete();
        } else if ($table == 'salesperson_projects') {
            DB::table('salesperson_projects')->whereIn('project_id', $projectIds)->delete();
        } else if ($table == 'salespersons_customers') {
            DB::table('salespersons_customers')->whereIn('customer_uid', $customer_ids)->delete();
        } else if ($table == 'section_area_of_works') {
            SectionAreaOfWorkEloquentModel::whereIn('document_id', $document_ids)->forceDelete();
        } else if ($table == 'quotation_template_items') {
            QuotationTemplateItemsEloquentModel::whereIn('document_id', $document_ids)->forceDelete();
        } else if ($table == 'aow_index' || $table == 'items_index' || $table == 'section_index') {
            DB::table($table)->whereIn('document_id', $document_ids)->delete();
        } else if ($table == 'sections') {
            SectionsEloquentModel::whereIn('document_id', $document_ids)->forceDelete();
        } else if ($table == 'tax_invoices') {
            TaxInvoiceEloquentModel::whereIn('project_id', $projectIds)->forceDelete();
        } else if ($table == 'vendor_invoices') {
            DB::table('vendor_invoices')->whereIn('purchase_order_id', $purchase_order_ids)->delete();
        } else if ($table == 'vendor_payments') {
            DB::table('vendor_payments')->whereIn('id', $vendor_payment_ids)->delete();
        } else if ($table == 'projects') {
            ProjectEloquentModel::whereIn('id', $projectIds)->forceDelete();
        }
    }
}
