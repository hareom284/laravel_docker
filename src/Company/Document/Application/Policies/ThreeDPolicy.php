<?php

namespace Src\Company\Document\Application\Policies;

class ThreeDPolicy
{

    public static function view()
    {
        return auth('sanctum')->user()->hasPermission('view_3D_design_document');
    }

    public static function store()
    {
        return auth('sanctum')->user()->hasPermission('create_3D_design_document');
    }

    public static function update()
    {
        return auth('sanctum')->user()->hasPermission('update_3D_design_document');
    }

    public static function destroy()
    {
        return auth('sanctum')->user()->hasPermission('delete_3D_design_document');
    }
}
