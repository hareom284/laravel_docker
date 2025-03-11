<?php

namespace Src\Company\Project\Application\Policies;

class TermAndConditionPolicy
{

    public static function view()
    {

        return auth('sanctum')->user()->hasPermission('view_term_and_conditions');
    }

    public static function store()
    {
        return auth('sanctum')->user()->hasPermission('create_term_and_conditions');
    }

    public static function update()
    {
        return auth('sanctum')->user()->hasPermission('update_term_and_conditions');
    }

    public static function destroy()
    {
        return auth('sanctum')->user()->hasPermission('delete_term_and_conditions');
    }
}
