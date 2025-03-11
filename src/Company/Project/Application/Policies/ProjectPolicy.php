<?php

namespace Src\Company\Project\Application\Policies;

class ProjectPolicy
{

    public static function view()
    {

        return auth('sanctum')->user()->hasPermission('view_project');
    }

    public static function view_ongoing_project()
    {

        return auth('sanctum')->user()->hasPermission('view_ongoing_project');
    }

    public static function view_by_management()
    {

        return auth('sanctum')->user()->hasPermission('view__project_by_management');
    }

    public static function view_project_report()
    {

        return auth('sanctum')->user()->hasPermission('view_project_report');
    }

    public static function store()
    {
        return auth('sanctum')->user()->hasPermission('create_project');
    }
    
    public static function update()
    {
        return auth('sanctum')->user()->hasPermission('update_project');
    }

    public static function destroy()
    {
        return auth('sanctum')->user()->hasPermission('delete_project');
    }
}
