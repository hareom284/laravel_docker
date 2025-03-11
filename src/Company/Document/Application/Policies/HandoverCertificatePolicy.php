<?php

namespace Src\Company\Document\Application\Policies;

class HandoverCertificatePolicy
{

    public static function view()
    {

        return auth('sanctum')->user()->hasPermission('view_project_handover_document');
    }

    public static function view_by_project()
    {

        return auth('sanctum')->user()->hasPermission('view_handover_document_by_project');
    }

    public static function store()
    {
        return auth('sanctum')->user()->hasPermission('generate_project_handover_document');
    }

    public static function sign_by_customer()
    {
        return auth('sanctum')->user()->hasPermission('sign_project_handover_document_by_customer');
    }

    public static function sign_by_manager()
    {
        return auth('sanctum')->user()->hasPermission('sign_project_handover_document_by_manager');
    }

    public static function destroy()
    {
        return auth('sanctum')->user()->hasPermission('delete_project_handover_document');
    }

}
