<?php

namespace Src\Company\Security\Application\Policies;

class PermissionPolicy
{

    public static function view()
    {

        return auth()->user()->hasPermission('access_permission');
    }

    public static function create()
    {
        return auth()->user()->hasPermission('create_permission');
    }
    public static function store()
    {
        return auth()->user()->hasPermission('create_permission');
    }
    public static function edit()
    {
        return auth()->user()->hasPermission('edit_permission');
    }

    public static function update()
    {
        return auth()->user()->hasPermission('edit_permission');
    }

    public static function destroy()
    {
        return auth()->user()->hasPermission('delete_permission');
    }
}
