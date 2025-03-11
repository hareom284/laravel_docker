<?php

namespace Src\Company\Document\Application\Policies;

class VOPolicy
{

    public static function view()
    {
        return auth('sanctum')->user()->hasPermission('view_project_vo_document');
    }

    public static function store()
    {
        return auth('sanctum')->user()->hasPermission('create_project_vo_document');
    }

    public static function update()
    {
        return auth('sanctum')->user()->hasPermission('update_project_vo_document');
    }

    public static function destroy()
    {
        return auth('sanctum')->user()->hasPermission('delete_project_vo_document');
    }
}
