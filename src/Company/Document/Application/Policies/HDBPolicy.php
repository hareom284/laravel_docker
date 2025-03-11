<?php

namespace Src\Company\Document\Application\Policies;

class HDBPolicy
{

    public static function view()
    {

        return auth('sanctum')->user()->hasPermission('view_HDB');
    }

    public static function store()
    {
        return auth('sanctum')->user()->hasPermission('create_HDB');
    }
    
    public static function update()
    {
        return auth('sanctum')->user()->hasPermission('update_HDB');
    }

    public static function destroy()
    {
        return auth('sanctum')->user()->hasPermission('delete_HDB');
    }

}
