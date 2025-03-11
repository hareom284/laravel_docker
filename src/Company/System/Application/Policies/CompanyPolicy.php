<?php

namespace Src\Company\System\Application\Policies;

class CompanyPolicy
{

    public static function view()
    {

        return auth('sanctum')->user()->hasPermission('view_company');
    }

    public static function store()
    {
        return auth('sanctum')->user()->hasPermission('create_company');
    }
    
    public static function update()
    {
        return auth('sanctum')->user()->hasPermission('update_company');
    }

    public static function destroy()
    {
        return auth('sanctum')->user()->hasPermission('delete_company');
    }
}
