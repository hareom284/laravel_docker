<?php

namespace Src\Company\Security\Application\Policies;


class UserPolicy
{

    public  static function view()
    {
        return auth()->user()->hasPermission('access_user');
    }

    public static function create()
    {
        return auth()->user()->hasPermission('create_user');
    }
    public static function store()
    {
        return auth()->user()->hasPermission('create_user');
    }
    public static function edit()
    {
        return auth()->user()->hasPermission('edit_user');
    }

    public static function update()
    {
        return auth()->user()->hasPermission('edit_user');
    }

    public static function destroy()
    {
        return auth()->user()->hasPermission('delete_user');
    }
}
