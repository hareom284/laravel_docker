<?php

namespace Src\Company\Document\Application\Policies;

class VendorPolicy
{

    public static function view()
    {
        return auth('sanctum')->user()->hasPermission('view_vendor_list');
    }

    public static function store()
    {
        return auth('sanctum')->user()->hasPermission('create_vendor');
    }

    public static function update()
    {
        return auth('sanctum')->user()->hasPermission('update_vendor');
    }

    public static function destroy()
    {
        return auth('sanctum')->user()->hasPermission('delete_vendor');
    }
}
