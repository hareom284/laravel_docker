<?php

namespace Src\Company\Document\Application\Policies;

class TaxInvoicePolicy
{

    public static function view()
    {
        return auth('sanctum')->user()->hasPermission('view_tax_invoice');
    }

    // public static function store()
    // {
    //     return auth('sanctum')->user()->hasPermission('create_3D_design_document');
    // }

    public static function sign_by_salesperson()
    {
        return auth('sanctum')->user()->hasPermission('sign_tax_invoice_by_salesperson');
    }

    public static function sign_by_manager()
    {
        return auth('sanctum')->user()->hasPermission('sign_tax_invoice_by_manager');
    }

    // public static function update()
    // {
    //     return auth('sanctum')->user()->hasPermission('update_3D_design_document');
    // }

    // public static function destroy()
    // {
    //     return auth('sanctum')->user()->hasPermission('delete_3D_design_document');
    // }
}
