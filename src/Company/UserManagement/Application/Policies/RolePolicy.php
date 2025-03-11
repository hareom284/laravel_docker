<?php

namespace Src\Company\UserManagement\Application\Policies;

class RolePolicy
{

    public static function view()
    {

        return auth('sanctum')->user()->hasPermission('view_role');
    }

    public static function store()
    {
        return auth('sanctum')->user()->hasPermission('create_role');
    }

    public static function update()
    {
        return auth('sanctum')->user()->hasPermission('update_role');
    }

    public static function destroy()
    {
        return auth('sanctum')->user()->hasPermission('delete_role');
    }
}
