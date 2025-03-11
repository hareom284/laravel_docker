<?php

namespace Src\Company\Document\Application\Policies;

class EvoTemplatePolicy
{

    public static function view()
    {

        return auth('sanctum')->user()->hasPermission('view_EVO_template');
    }

    public static function store()
    {

        return auth('sanctum')->user()->hasPermission('create_EVO_template');
    }

    public static function update()
    {
        return auth('sanctum')->user()->hasPermission('update_EVO_template');
    }

    public static function destroy()
    {
        return auth('sanctum')->user()->hasPermission('delete_EVO_template');
    }
}
