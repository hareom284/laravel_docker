<?php

namespace Src\Company\UserManagement\Application\Policies;

class TeamPolicy
{

    public static function view()
    {

        return auth('sanctum')->user()->hasPermission('view_team');
    }

    public static function store()
    {
        return auth('sanctum')->user()->hasPermission('create_team');
    }

    public static function update()
    {
        return auth('sanctum')->user()->hasPermission('update_team');
    }

    public static function destroy()
    {
        return auth('sanctum')->user()->hasPermission('delete_team');
    }
}
