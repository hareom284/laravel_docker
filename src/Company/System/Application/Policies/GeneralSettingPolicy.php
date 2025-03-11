<?php

namespace Src\Company\System\Application\Policies;

class GeneralSettingPolicy
{

    public static function view()
    {
        return auth('sanctum')->user()->hasPermission('view_site_theme');
    }
    
    public static function update()
    {
        return auth('sanctum')->user()->hasPermission('update_site_theme');
    }

}
