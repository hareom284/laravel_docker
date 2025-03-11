<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Src\Company\UserManagement\Infrastructure\EloquentModels\PermissionEloquentModel;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            [
                'name' => 'create_user',
                "guard_name" => "api"
            ],
            [
                'name' => 'view_user',
                "guard_name" => "api"
            ],
            [
                'name' => 'update_user',
                "guard_name" => "api"
            ],
            [
                'name' => 'delete_user',
                "guard_name" => "api"
            ],
            [
                'name' => "update_profile",
                "guard_name" => "api"
            ],
            [
                'name' => 'create_company',
                "guard_name" => "api"
            ],
            [
                'name' => 'view_company',
                "guard_name" => "api"
            ],
            [
                'name' => 'update_company',
                "guard_name" => "api"
            ],
            [
                'name' => 'delete_company',
                "guard_name" => "api"
            ],
            [
                'name' => 'create_role',
                "guard_name" => "api"
            ],
            [
                "name" => "view_role",
                "guard_name" => "api"
            ],
            [
                "name" => "update_role",
                "guard_name" => "api"
            ],
            [
                "name" => "delete_role",
                "guard_name" => "api"
            ],
            [
                "name" => "view_system_settings",
                "guard_name" => "api"
            ],
            [
                "name" => "update_system_settings",
                "guard_name" => "api"
            ],
            [
                "name" => "view_site_theme",
                "guard_name" => "api"
            ],
            [
                "name" => "update_site_theme",
                "guard_name" => "api"
            ],
            [
                "name" => "create_project",
                "guard_name" => "api"
            ],
            [
                "name" => "update_project",
                "guard_name" => "api"
            ],
            [
                "name" => "view_project",
                "guard_name" => "api"
            ],
            [
                "name" => "view_ongoing_project",
                "guard_name" => "api"
            ],
            [
                "name" => "view__project_by_management",
                "guard_name" => "api"
            ],
            [
                "name" => "view_user_management",
                "guard_name" => "api"
            ],
            [
                "name" => "delete_project",
                "guard_name" => "api"
            ],
            [
                "name" => "create_lead",
                "guard_name" => "api"
            ],
            [
                "name" => "update_lead",
                "guard_name" => "api"
            ],
            [
                "name" => "view_lead",
                "guard_name" => "api"
            ],
            [
                "name" => "change_customer_status",
                "guard_name" => "api"
            ],
            [
                "name" => "view_checklist_items",
                "guard_name" => "api"
            ],
            [
                "name" => "create_checklist_items",
                "guard_name" => "api"
            ],
            [
                "name" => "update_checklist_items",
                "guard_name" => "api"
            ],
            [
                "name" => "delete_checklist_items",
                "guard_name" => "api"
            ],
            [
                "name" => "view_yearly_kpi",
                "guard_name" => "api"
            ],
            [
                "name" => "view_monthly_kpi",
                "guard_name" => "api"
            ],
            [
                "name" => "update_monthly_kpi",
                "guard_name" => "api"
            ],
            [
                "name" => "view_salesperson_monthly_kpi",
                "guard_name" => "api"
            ],
            [
                "name" => "store_salesperson_monthly_kpi",
                "guard_name" => "api"
            ],
            [
                "name" => "view_salesperson_yearly_kpi",
                "guard_name" => "api"
            ],
            [
                "name" => "store_salesperson_yearly_kpi",
                "guard_name" => "api"
            ],
            [
                "name" => "view_rank",
                "guard_name" => "api"
            ],
            [
                "name" => "store_rank",
                "guard_name" => "api"
            ],
            [
                "name" => "update_rank",
                "guard_name" => "api"
            ],
            [
                "name" => "delete_rank",
                "guard_name" => "api"
            ],
            [
                "name" => "view_individual_report",
                "guard_name" => "api"
            ],
            [
                'name' => "create_calendar_events",
                "guard_name" => "api"
            ],
            [
                'name' => "update_calendar_events",
                "guard_name" => "api"
            ],
            [
                'name' => "view_calendar_events",
                "guard_name" => "api"
            ],
            [
                'name' => "delete_calendar_events",
                "guard_name" => "api"
            ],
            [
                'name' => "view_project_cover_document",
                "guard_name" => "api"
            ],
            [
                'name' => "create_project_requirement_document",
                "guard_name" => "api"
            ],
            [
                'name' => "view_project_requirement_document",
                "guard_name" => "api"
            ],
            [
                'name' => "update_project_requirement_document",
                "guard_name" => "api"
            ],
            [
                'name' => "delete_project_requirement_document",
                "guard_name" => "api"
            ],
            [
                'name' => "create_project_quotation_document",
                "guard_name" => "api"
            ],
            [
                'name' => "update_project_quotation_document",
                "guard_name" => "api"
            ],
            [
                'name' => "sign_project_quotation_document",
                "guard_name" => "api"
            ],
            [
                'name' => "view_project_quotation_document",
                "guard_name" => "api"
            ],
            [
                'name' => "delete_project_quotation_document",
                "guard_name" => "api"
            ],
            [
                'name' => "generate_project_contract_document",
                "guard_name" => "api"
            ],
            [
                'name' => "sign_project_contract_document",
                "guard_name" => "api"
            ],
            [
                'name' => "view_project_contract_document",
                "guard_name" => "api"
            ],
            [
                'name' => "create_project_design_work_document",
                "guard_name" => "api"
            ],
            [
                'name' => "update_project_design_work_document",
                "guard_name" => "api"
            ],
            [
                'name' => "sign_project_design_work_document",
                "guard_name" => "api"
            ],
            [
                'name' => "delete_project_design_work_document",
                "guard_name" => "api"
            ],
            [
                'name' => "view_project_design_work_document",
                "guard_name" => "api"
            ],
            [
                'name' => "create_project_work_order_document",
                "guard_name" => "api"
            ],
            [
                'name' => "update_project_work_order_document",
                "guard_name" => "api"
            ],
            [
                'name' => "sign_project_work_order_document",
                "guard_name" => "api"
            ],
            [
                'name' => "delete_project_work_order_document",
                "guard_name" => "api"
            ],
            [
                'name' => "view_project_work_order_document",
                "guard_name" => "api"
            ],
            [
                'name' => "generate_project_handover_document",
                "guard_name" => "api"
            ],
            [
                'name' => "sign_project_handover_document_by_customer",
                "guard_name" => "api"
            ],
            [
                'name' => "sign_project_handover_document_by_manager",
                "guard_name" => "api"
            ],
            [
                'name' => "view_project_handover_document",
                "guard_name" => "api"
            ],
            [
                'name' => "delete_project_handover_document",
                "guard_name" => "api"
            ],
            [
                'name' => "view_handover_document_by_project",
                "guard_name" => "api"
            ],
            [
                'name' => "create_project_vo_document",
                "guard_name" => "api"
            ],
            [
                'name' => "sign_project_vo_document",
                "guard_name" => "api"
            ],
            [
                'name' => "update_project_vo_document",
                "guard_name" => "api"
            ],
            [
                'name' => "view_project_vo_document",
                "guard_name" => "api"
            ],
            [
                'name' => "delete_project_vo_document",
                "guard_name" => "api"
            ],
            [
                'name' => "create_project_evo_document",
                "guard_name" => "api"
            ],
            [
                'name' => "sign_project_evo_document",
                "guard_name" => "api"
            ],
            [
                'name' => "update_project_evo_document",
                "guard_name" => "api"
            ],
            [
                'name' => "view_project_evo_document",
                "guard_name" => "api"
            ],
            [
                'name' => "delete_project_evo_document",
                "guard_name" => "api"
            ],
            [
                'name' => "create_project_foc_document",
                "guard_name" => "api"
            ],
            [
                'name' => "sign_project_foc_document",
                "guard_name" => "api"
            ],
            [
                'name' => "update_project_foc_document",
                "guard_name" => "api"
            ],
            [
                'name' => "view_project_foc_document",
                "guard_name" => "api"
            ],
            [
                'name' => "create_project_cancellation_document",
                "guard_name" => "api"
            ],
            [
                'name' => "sign_project_cancellation_document",
                "guard_name" => "api"
            ],
            [
                'name' => "update_project_cancellation_document",
                "guard_name" => "api"
            ],
            [
                'name' => "view_project_cancellation_document",
                "guard_name" => "api"
            ],
            [
                'name' => "update_project_milestone",
                "guard_name" => "api"
            ],
            [
                'name' => "view_project_milestone",
                "guard_name" => "api"
            ],
            [
                'name' => "update_project_timeline",
                "guard_name" => "api"
            ],
            [
                'name' => "view_project_timeline",
                "guard_name" => "api"
            ],
            [
                'name' => "view_project_settings",
                "guard_name" => "api"
            ],
            [
                'name' => "update_project_settings",
                "guard_name" => "api"
            ],
            [
                'name' => "update_personal_quotation_template",
                "guard_name" => "api"
            ],
            [
                'name' => "view_personal_quotation_template",
                "guard_name" => "api"
            ],
            [
                'name' => "view_EVO_template",
                "guard_name" => "api"
            ],
            [
                'name' => "create_EVO_template",
                "guard_name" => "api"
            ],
            [
                'name' => "update_EVO_template",
                "guard_name" => "api"
            ],
            [
                'name' => "delete_EVO_template",
                "guard_name" => "api"
            ],
            [
                'name' => "create_additional_folder",
                "guard_name" => "api"
            ],
            [
                'name' => "create_additional_document",
                "guard_name" => "api"
            ],
            [
                'name' => "view_project_report",
                "guard_name" => "api"
            ],
            [
                'name' => "view_notification",
                "guard_name" => "api"
            ],
            [
                'name' => "view_purchase_order",
                "guard_name" => "api"
            ],
            [
                'name' => "create_purchase_order",
                "guard_name" => "api"
            ],
            [
                'name' => "update_purchase_order",
                "guard_name" => "api"
            ],
            [
                'name' => "approve_purchase_order",
                "guard_name" => "api"
            ],
            [
                'name' => "delete_purchase_order",
                "guard_name" => "api"
            ],
            [
                'name' => "delete_purchase_order_item",
                "guard_name" => "api"
            ],
            [
                'name' => "create_purchase_order_template_item",
                "guard_name" => "api"
            ],
            [
                'name' => "view_purchase_order_template_item",
                "guard_name" => "api"
            ],
            [
                'name' => "update_purchase_order_template_item",
                "guard_name" => "api"
            ],
            [
                'name' => "delete_purchase_order_template_item",
                "guard_name" => "api"
            ],
            [
                'name' => "update_company_kpi",
                "guard_name" => "api"
            ],
            [
                'name' => "view_company_kpi",
                "guard_name" => "api"
            ],
            [
                'name' => "update_salesperson_kpi",
                "guard_name" => "api"
            ],
            [
                'name' => "view_salesperson_kpi",
                "guard_name" => "api"
            ],
            [
                'name' => "update_salesperson_rank",
                "guard_name" => "api"
            ],
            [
                'name' => "view_salesperson_commission",
                "guard_name" => "api"
            ],
            [
                'name' => "create_salesperson_commission",
                "guard_name" => "api"
            ],
            [
                'name' => "update_salesperson_commission",
                "guard_name" => "api"
            ],
            [
                'name' => "delete_salesperson_commission",
                "guard_name" => "api"
            ],
            [
                'name' => "view_FAQ",
                "guard_name" => "api"
            ],
            [
                'name' => "update_FAQ",
                "guard_name" => "api"
            ],
            [
                'name' => "delete_FAQ",
                "guard_name" => "api"
            ],
            [
                'name' => "update_company_quotation_template",
                "guard_name" => "api"
            ],
            [
                'name' => "view_company_quotation_template",
                "guard_name" => "api"
            ],
            [
                'name' => "create_company_quotation_template",
                "guard_name" => "api"
            ],
            [
                'name' => "delete_company_quotation_template",
                "guard_name" => "api"
            ],
            [
                'name' => "view_company_EVO_template",
                "guard_name" => "api"
            ],
            [
                'name' => "update_company_EVO_template",
                "guard_name" => "api"
            ],
            [
                'name' => "view_project_sales_report",
                "guard_name" => "api"
            ],
            [
                'name' => "update_project_sales_report",
                "guard_name" => "api"
            ],
            [
                'name' => "view_salesperson_report",
                "guard_name" => "api"
            ],
            [
                'name' => "view_company_report",
                "guard_name" => "api"
            ],
            [
                'name' => "view_company_finance_documents",
                "guard_name" => "api"
            ],
            [
                'name' => "create_company_finance_documents",
                "guard_name" => "api"
            ],
            [
                'name' => "update_company_finance_documents",
                "guard_name" => "api"
            ],
            [
                'name' => "delete_company_finance_documents",
                "guard_name" => "api"
            ],
            [
                'name' => "view_design_work_document",
                "guard_name" => "api"
            ],
            [
                'name' => "create_3D_design_document",
                "guard_name" => "api"
            ],
            [
                'name' => "update_3D_design_document",
                "guard_name" => "api"
            ],
            [
                'name' => "delete_3D_design_document",
                "guard_name" => "api"
            ],
            [
                'name' => "view_3D_design_document",
                "guard_name" => "api"
            ],
            [
                'name' => "view_timeline",
                "guard_name" => "api"
            ],
            [
                'name' => "view_project_requirements_document",
                "guard_name" => "api"
            ],
            [
                'name' => "view_review",
                "guard_name" => "api"
            ],
            [
                'name' => "create_review",
                "guard_name" => "api"
            ],
            [
                'name' => "accept_help_center",
                "guard_name" => "api"
            ],
            [
                'name' => "create_question_ticket",
                "guard_name" => "api"
            ],
            //access sale role admin panel
            [
                'name' => "access_sale_dashboard",
                "guard_name" => "api"
            ],
            [
                'name' => "access_sale_project",
                "guard_name" => "api"
            ],
            [
                'name' => "access_sale_contacts",
                "guard_name" => "api"
            ],
            [
                'name' => "access_sale_report",
                "guard_name" => "api"
            ],
            [
                'name' => "access_admin",
                "guard_name" => "api"

            ],
            //marketing role
            [
                'name' => "access_marketing_lead_management",
                "guard_name" => "api"
            ],
            //salemanagement role
            [
                'name' => "access_dashboard",
                "guard_name" => "api"
            ],
            [
                'name' => "access_mng_project",
                "guard_name" => "api"
            ],
            [
                'name' => "access_sale_calendar",
                "guard_name" => "api"
            ],
            [
                'name' => "access_sale_report_dashboard",
                "guard_name" => "api"
            ],
            [
                'name' => "access_management_dashboard",
                "guard_name" => "api"
            ],
            [
                'name' => "access_management_marketing",
                "guard_name" => "api"
            ],

            [
                'name' => "access_mng_help_center",
                "guard_name" => "api"
            ],
            [
                'name' => "access_company_index",
                "guard_name" => "api"
            ],
            [
                'name' => "access_approve_document",
                "guard_name" => "api"
            ],

            //customer role
            [
                'name' => "access_cus_project",
                "guard_name" => "api"
            ],
            [
                'name' => "access_help_center",
                "guard_name" => "api"
            ],
            //drafter role
            [
                'name' => "access_dft_project",
                "guard_name" => "api"
            ],
            [
                'name' => "access_dft_help_center",
                "guard_name" => "api"
            ],
            //superadmin role
            [
                'name' => "access_admin_home",
                "guard_name" => "api"
            ],
            [
                'name' => "access_admin_user",
                "guard_name" => "api"
            ],
            [
                'name' => "access_admin_company",
                "guard_name" => "api"
            ],
            [
                'name' => "access_admin_role_permission",
                "guard_name" => "api"
            ],
            [
                'name' => "access_admin_setting",
                "guard_name" => "api"
            ],
            [
                'name' => "access_admin_site_theme",
                "guard_name" => "api"
            ],
            [
                'name' => "access_management_admin",
                "guard_name" => "api"
            ],

            //accountant role
            [
                'name' => "access_acc_saleperson_report",
                "guard_name" => "api"
            ],
            [
                'name' => "access_acc_calendar",
                "guard_name" => "api"
            ],
            [
                'name' => "access_acc_dashboard",
                "guard_name" => "api"
            ],
            [
                'name' => "access_acc_company_report",
                "guard_name" => "api"
            ],
            [
                'name' => "access_acc_purchase_orders",
                "guard_name" => "api"
            ],
            [
                'name' => "view_HDB",
                "guard_name" => "api"
            ],
            [
                'name' => "create_HDB",
                "guard_name" => "api"
            ],
            [
                'name' => "update_HDB",
                "guard_name" => "api"
            ],
            [
                'name' => "delete_HDB",
                "guard_name" => "api"
            ],
            [
                'name' => "view_document",
                "guard_name" => "api"
            ],
            [
                'name' => "create_document",
                "guard_name" => "api"
            ],
            [
                'name' => "update_document",
                "guard_name" => "api"
            ],
            [
                'name' => "delete_document",
                "guard_name" => "api"
            ],
            [
                'name' => "view_document_standard",
                "guard_name" => "api"
            ],
            [
                'name' => "create_document_standard",
                "guard_name" => "api"
            ],
            [
                'name' => "update_document_standard",
                "guard_name" => "api"
            ],
            [
                'name' => "delete_document_standard",
                "guard_name" => "api"
            ],
            [
                'name' => "view_vendor_category",
                "guard_name" => "api"
            ],
            [
                'name' => "create_vendor_category",
                "guard_name" => "api"
            ],
            [
                'name' => "update_vendor_category",
                "guard_name" => "api"
            ],
            [
                'name' => "delete_vendor_category",
                "guard_name" => "api"
            ],
            [
                'name' => "view_vendor_list",
                "guard_name" => "api"
            ],
            [
                'name' => "create_vendor",
                "guard_name" => "api"
            ],
            [
                'name' => "update_vendor",
                "guard_name" => "api"
            ],
            [
                'name' => "delete_vendor",
                "guard_name" => "api"
            ],
            [
                'name' => "view_tax_invoice",
                "guard_name" => "api"
            ],
            [
                'name' => "sign_tax_invoice_by_salesperson",
                "guard_name" => "api"
            ],
            [
                'name' => "sign_tax_invoice_by_manager",
                "guard_name" => "api"
            ],
            [
                'name' => "view_sale_report",
                "guard_name" => "api"
            ],
            [
                'name' => "create_sale_report",
                "guard_name" => "api"
            ],
            [
                'name' => "update_sale_report",
                "guard_name" => "api"
            ],
            [
                'name' => "view_customer_payment",
                "guard_name" => "api"
            ],
            [
                'name' => "create_customer_payment",
                "guard_name" => "api"
            ],
            [
                'name' => "update_customer_payment",
                "guard_name" => "api"
            ],
            [
                'name' => "delete_customer_payment",
                "guard_name" => "api"
            ],
            [
                'name' => "view_supplier_costing",
                "guard_name" => "api"
            ],
            [
                'name' => "create_supplier_costing",
                "guard_name" => "api"
            ],
            [
                'name' => "update_supplier_costing",
                "guard_name" => "api"
            ],
            [
                'name' => "delete_supplier_costing",
                "guard_name" => "api"
            ],
            [
                'name' => "view_salesperson_kpi_report",
                "guard_name" => "api"
            ],
            [
                'name' => "view_pending_supplier_costing",
                "guard_name" => "api"
            ],
            [
                'name' => "sign_supplier_costing_by_manager",
                "guard_name" => "api"
            ],
            [
                'name' => "view_customer_FAQ",
                "guard_name" => "api"
            ],
            [
                'name' => "create_FAQ",
                "guard_name" => "api"
            ],
            [
                'name' => "reply_customer_FAQ",
                "guard_name" => "api"
            ],
            [
                'name' => "view_company_kpi_list",
                "guard_name" => "api"
            ],
            [
                'name' => "view_company_kpi_by_year",
                "guard_name" => "api"
            ],
            [
                'name' => "create_company_kpi",
                "guard_name" => "api"
            ],
            [
                'name' => "view_salesperson_list",
                "guard_name" => "api"
            ],
            [
                'name' => "access_cus_help_center",
                "guard_name" => "api"
            ],
            [
                'name' => "view_project_quotation",
                "guard_name" => "api"
            ],
            [
                'name' => "store_project_quotation",
                "guard_name" => "api"
            ],
            [
                'name' => "view_project_quotation_template",
                "guard_name" => "api"
            ],
            [
                'name' => "access_acc_vendors",
                "guard_name" => "api"
            ],
            [
                'name' => 'verify_supplier_costing',
                'guard_name' => 'api'
            ],
            [
                'name' => "access_vendor_dashboard", // vendor login for dashboard access
                "guard_name" => "api"
            ]
        ];

        foreach ($permissions as $permission) {
            $permissionModel = PermissionEloquentModel::firstOrCreate($permission);

            if ($permissionModel->wasRecentlyCreated) {
                echo "  Created: {$permission['name']}\n";
            }
        }
        info('Permissions seeder running!');
    }
}
