<?php

namespace Src\Company\Document\Application\Policies;

class ProjectQuotationOldPolicy
{

    public static function view()
    {

        return auth('sanctum')->user()->hasPermission('view_project_quotation');
    }

    public static function store()
    {
        return auth('sanctum')->user()->hasPermission('store_project_quotation');
    }
    public static function view_template()
    {
        return auth('sanctum')->user()->hasPermission('view_project_quotation_template');
    }
}
