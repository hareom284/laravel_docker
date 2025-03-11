<?php

namespace Src\Company\Document\Application\Policies;

class DocumentStandardPolicy
{

    public static function view()
    {

        return auth('sanctum')->user()->hasPermission('view_document_standard');
    }

    public static function store()
    {

        return auth('sanctum')->user()->hasPermission('create_document_standard');
    }

    public static function update()
    {
        return auth('sanctum')->user()->hasPermission('update_document_standard');
    }

    public static function destroy()
    {
        return auth('sanctum')->user()->hasPermission('delete_document_standard');
    }
}
