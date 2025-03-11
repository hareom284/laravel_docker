<?php

namespace Src\Company\System\Application\Policies;

class CompanyKpiPolicy
{

    public static function view()
    {

        return auth('sanctum')->user()->hasPermission('view_company_kpi_list');
    }

    public static function view_kpi_by_year()
    {

        return auth('sanctum')->user()->hasPermission('view_company_kpi_by_year');
    }

    public static function store()
    {
        return auth('sanctum')->user()->hasPermission('create_company_kpi');
    }

}
