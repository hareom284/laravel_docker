<?php

namespace Src\Company\Project\Application\Policies;

class SaleReportPolicy
{

    public static function view_sale_report()
    {

        return auth('sanctum')->user()->hasPermission('view_sale_report');
    }

    public static function view_salesperson_report()
    {

        return auth('sanctum')->user()->hasPermission('view_salesperson_report');
    }

    public static function view_salesperson_kpi_report()
    {

        return auth('sanctum')->user()->hasPermission('view_salesperson_kpi_report');
    }

    public static function store_sale_report()
    {
        return auth('sanctum')->user()->hasPermission('create_sale_report');
    }

    public static function update_sale_report()
    {
        return auth('sanctum')->user()->hasPermission('update_sale_report');
    }

    public static function view_customer_payment()
    {
        return auth('sanctum')->user()->hasPermission('view_customer_payment');
    }

    public static function store_customer_payment()
    {
        return auth('sanctum')->user()->hasPermission('create_customer_payment');
    }

    public static function update_customer_payment()
    {
        return auth('sanctum')->user()->hasPermission('update_customer_payment');
    }

    public static function destroy_customer_payment()
    {
        return auth('sanctum')->user()->hasPermission('delete_customer_payment');
    }

    public static function view_supplier_costing()
    {
        return auth('sanctum')->user()->hasPermission('view_supplier_costing');
    }

    public static function view_pending_supplier_costing()
    {
        return auth('sanctum')->user()->hasPermission('view_pending_supplier_costing');
    }

    public static function store_supplier_costing()
    {
        return auth('sanctum')->user()->hasPermission('create_supplier_costing');
    }

    public static function verify_supplier_costing()
    {
        return auth('sanctum')->user()->hasPermission('verify_supplier_costing');
    }

    public static function sign_supplier_costing_manager()
    {
        return auth('sanctum')->user()->hasPermission('sign_supplier_costing_by_manager');
    }

    public static function update_supplier_costing()
    {
        return auth('sanctum')->user()->hasPermission('update_supplier_costing');
    }

    public static function destroy_supplier_costing()
    {
        return auth('sanctum')->user()->hasPermission('delete_supplier_costing');
    }

}
