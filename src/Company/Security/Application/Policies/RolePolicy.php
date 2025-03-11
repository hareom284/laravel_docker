<?php

namespace Src\Company\Security\Application\Policies;

class RolePolicy
{

    public static function view()
    {
        return auth()->user()->hasPermission('access_role');
    }

    public static function create()
    {
        return auth()->user()->hasPermission('create_role');
    }
    public static function store()
    {
        return auth()->user()->hasPermission('create_role');
    }
    public static function edit()
    {
        return auth()->user()->hasPermission('edit_role');
    }

    public static function update()
    {
        return auth()->user()->hasPermission('edit_role');
    }

    public static function destroy()
    {
        return auth()->user()->hasPermission('delete_role');
    }

}
