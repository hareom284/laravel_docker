<?php

namespace Src\Company\Document\Application\Policies;

class ProjectQuotationPolicy
{

    public static function view()
    {

        return auth('sanctum')->user()->hasPermission('view_project_quotation_document');
    }

    public static function store()
    {
        return auth('sanctum')->user()->hasPermission('create_project_quotation_document');
    }
    
    public static function update()
    {
        return auth('sanctum')->user()->hasPermission('update_project_quotation_document');
    }

    public static function sign()
    {
        return auth('sanctum')->user()->hasPermission('sign_project_quotation_document');
    }

    public static function destroy()
    {
        return auth('sanctum')->user()->hasPermission('delete_project_quotation_document');
    }

    public static function view_template()
    {
        return auth('sanctum')->user()->hasPermission('view_company_quotation_template');
    }

    public static function store_template()
    {
        return auth('sanctum')->user()->hasPermission('create_company_quotation_template');
    }

    public static function update_template()
    {
        return auth('sanctum')->user()->hasPermission('update_company_quotation_template');
    }

    public static function destroy_template()
    {
        return auth('sanctum')->user()->hasPermission('delete_company_quotation_template');
    }

    public static function view_personal_template()
    {
        return auth('sanctum')->user()->hasPermission('view_personal_quotation_template');
    }

    public static function update_personal_template()
    {
        return auth('sanctum')->user()->hasPermission('update_personal_quotation_template');
    }
}
