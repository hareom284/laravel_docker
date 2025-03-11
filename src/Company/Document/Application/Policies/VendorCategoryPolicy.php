<?php

namespace Src\Company\Document\Application\Policies;

class VendorCategoryPolicy
{

    public static function view()
    {

        return auth('sanctum')->user()->hasPermission('view_vendor_category');
    }

    public static function store()
    {

        return auth('sanctum')->user()->hasPermission('create_vendor_category');
    }

    public static function update()
    {
        return auth('sanctum')->user()->hasPermission('update_vendor_category');
    }

    public static function destroy()
    {
        return auth('sanctum')->user()->hasPermission('delete_vendor_category');
    }
}
