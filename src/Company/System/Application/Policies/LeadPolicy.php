<?php

namespace Src\Company\System\Application\Policies;

class LeadPolicy
{

    public static function view()
    {

        return auth('sanctum')->user()->hasPermission('view_lead');
    }

    public static function store()
    {
        return auth('sanctum')->user()->hasPermission('create_lead');
    }
    
    public static function update()
    {
        return auth('sanctum')->user()->hasPermission('update_lead');
    }

    public static function change_customer_status()
    {
        return auth('sanctum')->user()->hasPermission('change_customer_status');
    }
}
