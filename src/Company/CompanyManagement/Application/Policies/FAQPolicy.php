<?php

namespace Src\Company\CompanyManagement\Application\Policies;

class FAQPolicy
{

    public static function view()
    {

        return auth('sanctum')->user()->hasPermission('view_FAQ');
    }

    public static function view_customer_FAQ()
    {

        return auth('sanctum')->user()->hasPermission('view_customer_FAQ');
    }

    public static function store()
    {

        return auth('sanctum')->user()->hasPermission('create_FAQ');
    }

    public static function reply_customer_FAQ()
    {

        return auth('sanctum')->user()->hasPermission('reply_customer_FAQ');
    }

    public static function update()
    {
        return auth('sanctum')->user()->hasPermission('update_FAQ');
    }

    public static function destroy()
    {
        return auth('sanctum')->user()->hasPermission('delete_FAQ');
    }

}
