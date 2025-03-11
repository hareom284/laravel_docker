<?php

namespace Src\Company\System\Application\Repositories\Eloquent;

use Illuminate\Support\Facades\DB;
use Src\Company\System\Domain\Model\Entities\SiteSetting;
use Src\Company\System\Domain\Resources\SiteSettingResource;
use Src\Company\System\Domain\Resources\LogoAndFaviconResource;
use Src\Company\System\Domain\Repositories\SiteSettingRepositoryInterface;
use Src\Company\System\Infrastructure\EloquentModels\SiteSettingEloquentModel;

class SiteSettingRepository implements SiteSettingRepositoryInterface
{
    public function findById($id)
    {
        $settingEloquent = SiteSettingEloquentModel::where('id',$id)->first();

        $setting = new SiteSettingResource($settingEloquent);

        return $setting;
    }

    public function findLogoAndFavicon()
    {
        $siteEloquent = SiteSettingEloquentModel::first();

        $setting = new LogoAndFaviconResource($siteEloquent);

        return $setting;
    }

    public function update(SiteSetting $site): SiteSetting
    {
        $siteEloquent = SiteSettingEloquentModel::query()->findOrFail($site->id);

        $siteEloquent->site_name = $site->site_name;

        $siteEloquent->ssl = $site->ssl;

        $siteEloquent->timezone = $site->timezone;

        $siteEloquent->locale = $site->locale;

        $siteEloquent->url = $site->url;

        $siteEloquent->email = $site->email;

        $siteEloquent->contact_number = $site->contact_number;



        if (request()->hasFile('website_logo') && request()->file('website_logo')->isValid()) {

            $siteEloquent->clearMediaCollection('website_logo');
            $siteEloquent->addMediaFromRequest('website_logo')->toMediaCollection('website_logo', 'media_sitelogo');
            $siteEloquent->website_logo = $siteEloquent->getFirstMediaUrl('website_logo');
        }

        if (request()->hasFile('website_favicon') && request()->file('website_favicon')->isValid()) {

            $siteEloquent->clearMediaCollection('website_favicon');
            $siteEloquent->addMediaFromRequest('website_favicon')->toMediaCollection('website_favicon', 'media_sitefavico');

            $siteEloquent->website_favicon = $siteEloquent->getFirstMediaUrl('website_favicon');

        }

        $siteEloquent->save();

        return $site;
    }

}
