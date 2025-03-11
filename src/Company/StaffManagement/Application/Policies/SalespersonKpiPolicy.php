<?php

namespace Src\Company\StaffManagement\Application\Policies;

class SalespersonKpiPolicy
{

    public static function view_salesperson_monthly_kpi()
    {
        return auth('sanctum')->user()->hasPermission('view_salesperson_monthly_kpi');
    }

    public static function store_salesperson_monthly_kpi()
    {
        return auth('sanctum')->user()->hasPermission('store_salesperson_monthly_kpi');
    }
    
    public static function view_salesperson_yearly_kpi()
    {
        return auth()->user('sanctum')->hasPermission('view_salesperson_yearly_kpi');
    }

    public static function store_salesperson_yearly_kpi()
    {
        return auth('sanctum')->user()->hasPermission('store_salesperson_yearly_kpi');
    }
}
