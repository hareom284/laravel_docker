<?php

namespace Src\Company\Document\Application\Policies;

class PurchaseOrderTemplateItemPolicy
{

    public static function view()
    {

        return auth('sanctum')->user()->hasPermission('view_purchase_order_template_item');
    }

    public static function store()
    {
        return auth('sanctum')->user()->hasPermission('create_purchase_order_template_item');
    }
    
    public static function update()
    {
        return auth('sanctum')->user()->hasPermission('update_purchase_order_template_item');
    }

    public static function destroy()
    {
        return auth('sanctum')->user()->hasPermission('delete_purchase_order_template_item');
    }

}
