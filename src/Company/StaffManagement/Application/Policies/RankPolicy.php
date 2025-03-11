<?php

namespace Src\Company\StaffManagement\Application\Policies;

class RankPolicy
{

    public static function view()
    {

        return auth('sanctum')->user()->hasPermission('view_rank');
    }

    public static function store()
    {
        return auth('sanctum')->user()->hasPermission('store_rank');
    }
    
    public static function update()
    {
        return auth('sanctum')->user()->hasPermission('update_rank');
    }

    public static function destroy()
    {
        return auth('sanctum')->user()->hasPermission('delete_rank');
    }
}
