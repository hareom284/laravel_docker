<?php

namespace Src\Company\CustomerManagement\Application\Policies;

class CustomerPolicy
{

    public static function view()
    {

        return auth('sanctum')->user()->hasPermission('view_user');
    }

    public static function view_salesperson()
    {

        return auth('sanctum')->user()->hasPermission('view_salesperson_list');
    }

    public static function store()
    {
        return auth('sanctum')->user()->hasPermission('create_user');
    }

    public static function update()
    {
        return auth('sanctum')->user()->hasPermission('update_user');
    }

    public static function update_lead()
    {
        return auth('sanctum')->user()->hasPermission('update_lead');
    }
    public static function create_lead()
    {
        return auth('sanctum')->user()->hasPermission('create_lead');
    }
    public static function delete_lead()
    {
        return auth('sanctum')->user()->hasPermission('delete_lead');
    }

    public static function update_profile()
    {
        return auth('sanctum')->user()->hasPermission('update_profile');
    }

    public static function destroy()
    {
        return auth('sanctum')->user()->hasPermission('delete_user');
    }
    public static function change_customer_status()
    {
        return auth('sanctum')->user()->hasPermission('change_customer_status');
    }
}
