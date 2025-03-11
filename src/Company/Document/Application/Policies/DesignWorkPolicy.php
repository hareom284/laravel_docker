<?php

namespace Src\Company\Document\Application\Policies;

class DesignWorkPolicy
{

    public static function view()
    {

        return auth('sanctum')->user()->hasPermission('view_project_design_work_document');
    }

    public static function store()
    {

        return auth('sanctum')->user()->hasPermission('create_project_design_work_document');
    }

    public static function update()
    {
        return auth('sanctum')->user()->hasPermission('update_project_design_work_document');
    }

    public static function sign()
    {
        return auth('sanctum')->user()->hasPermission('sign_project_design_work_document');
    }

    public static function destroy()
    {
        return auth('sanctum')->user()->hasPermission('delete_project_design_work_document');
    }
}
