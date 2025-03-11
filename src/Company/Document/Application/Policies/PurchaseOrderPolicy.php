<?php

namespace Src\Company\Document\Application\Policies;

class PurchaseOrderPolicy
{

    public static function view()
    {

        return auth('sanctum')->user()->hasPermission('view_purchase_order');
    }

    public static function store()
    {
        return auth('sanctum')->user()->hasPermission('create_purchase_order');
    }

    public static function update()
    {
        return auth('sanctum')->user()->hasPermission('update_purchase_order');
    }

    public static function sign()
    {
        return auth('sanctum')->user()->hasPermission('approve_purchase_order');
    }

    public static function destroy()
    {
        return auth('sanctum')->user()->hasPermission('delete_purchase_order');
    }

    public static function destroy_item()
    {
        return auth('sanctum')->user()->hasPermission('delete_purchase_order_item');
    }
}
