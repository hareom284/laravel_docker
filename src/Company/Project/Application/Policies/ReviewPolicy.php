<?php

namespace Src\Company\Project\Application\Policies;

class ReviewPolicy
{

    public static function view()
    {

        return auth('sanctum')->user()->hasPermission('view_review');
    }

    public static function store()
    {
        return auth('sanctum')->user()->hasPermission('create_review');
    }

}
