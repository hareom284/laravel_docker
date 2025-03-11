<?php

namespace Src\Company\Document\Application\Policies;

class ProjectContractPolicy
{

    public static function view()
    {

        return auth('sanctum')->user()->hasPermission('view_project_contract_document');
    }

    public static function generate()
    {
        return auth('sanctum')->user()->hasPermission('generate_project_contract_document');
    }

    public static function sign()
    {
        return auth('sanctum')->user()->hasPermission('sign_project_contract_document');
    }
}
