<?php

namespace Src\Company\Project\Application\Policies;

class EventPolicy
{

    public static function view()
    {

        return auth('sanctum')->user()->hasPermission('view_calendar_events');
    }

    public static function store()
    {
        return auth('sanctum')->user()->hasPermission('create_calendar_events');
    }
    
    public static function update()
    {
        return auth('sanctum')->user()->hasPermission('update_calendar_events');
    }

    public static function destroy()
    {
        return auth('sanctum')->user()->hasPermission('delete_calendar_events');
    }
}
