<?php

namespace Src\Company\System\Application\Policies;

class CampaignManagementPolicy
{

    public static function view()
    {

        return auth('sanctum')->user()->hasPermission('campaign_email_access');
    }

    public static function send()
    {

        return auth('sanctum')->user()->hasPermission('campaign_email_send');
    }

}
