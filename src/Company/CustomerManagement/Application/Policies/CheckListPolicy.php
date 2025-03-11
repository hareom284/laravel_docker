<?php

namespace Src\Company\CustomerManagement\Application\Policies;

class CheckListPolicy
{

    public static function view()
    {
        return auth('sanctum')->user()->hasPermission('view_checklist_items');
    }

    public static function store()
    {
        return auth('sanctum')->user()->hasPermission('create_checklist_items');
    }

    public static function update()
    {
        return auth()->user('sanctum')->hasPermission('update_checklist_items');
    }

    public static function destroy()
    {
        return auth('sanctum')->user()->hasPermission('delete_checklist_items');
    }
}
