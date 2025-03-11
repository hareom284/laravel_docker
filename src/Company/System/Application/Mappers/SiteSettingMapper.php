<?php

namespace Src\Company\System\Application\Mappers;
use Illuminate\Http\Request;
use Src\Company\System\Domain\Model\Entities\SiteSetting;
use Src\Company\System\Infrastructure\EloquentModels\SiteSettingEloquentModel;
use Illuminate\Support\Facades\Storage;

class SiteSettingMapper
{
    public static function fromRequest(Request $request, ?int $site_setting_id = null): SiteSetting
    {
        // if ($request->hasFile('website_logo')) {

        //     Storage::disk('public')->deleteDirectory('website_logo');

        //     $logoName =  'website_logo_' . time(). "." .$request->file('website_logo')->extension();

        //     $filePath = 'website_logo/' . $logoName;

        //     Storage::disk('public')->put($filePath, file_get_contents($request->file('website_logo')));

        //     $logo = $logoName;

        // } else {

        //     // Set logo to null if not provided
        //     $logo = null;
        // }

        // if ($request->hasFile('website_favicon')) {

        //     Storage::disk('public')->deleteDirectory('website_favicon');

        //     $favIconName =  'website_favicon_' .  time(). "." .$request->file('website_favicon')->extension();

        //     $favIconFilePath = 'website_favicon/' . $favIconName;

        //     Storage::disk('public')->put($favIconFilePath, file_get_contents($request->file('website_favicon')));

        //     $favicon = $favIconName;

        // } else {
        //     // Set logo to null if not provided
        //     $favicon = null;
        // }

        return new SiteSetting(
            id: $site_setting_id,
            site_name: $request->string('site_name'),
            ssl: $request->string('ssl'),
            timezone: $request->string('timezone'),
            locale: $request->string('locale'),
            url: $request->string('url'),
            email: $request->string('email'),
            contact_number: $request->string('contact_number'),
            website_logo: $request->string('website_logo') ?? null,
            website_favicon: $request->string('website_favicon') ?? null
        );
    }

    public static function fromEloquent(SiteSettingEloquentModel $siteSettingEloquent): SiteSetting
    {
        return new SiteSetting(
            id: $siteSettingEloquent->id,
            site_name: $siteSettingEloquent->site_name,
            ssl: $siteSettingEloquent->ssl,
            timezone: $siteSettingEloquent->timezone,
            locale: $siteSettingEloquent->locale,
            url: $siteSettingEloquent->url,
            email: $siteSettingEloquent->email,
            contact_number: $siteSettingEloquent->contact_number,
            website_logo: $siteSettingEloquent->website_logo,
            website_favicon: $siteSettingEloquent->website_favicon
        );
    }

    public static function toEloquent(SiteSetting $setting): SiteSettingEloquentModel
    {
        $siteSettingEloquent = new SiteSettingEloquentModel();
        if ($setting->id) {
            $siteSettingEloquent = SiteSettingEloquentModel::query()->findOrFail($setting->id);
        }
        $siteSettingEloquent->site_name = $setting->site_name;
        $siteSettingEloquent->ssl = $setting->ssl;
        $siteSettingEloquent->timezone = $setting->timezone;
        $siteSettingEloquent->locale = $setting->locale;
        $siteSettingEloquent->url = $setting->url;
        $siteSettingEloquent->email = $setting->email;
        $siteSettingEloquent->contact_number = $setting->contact_number;
        $siteSettingEloquent->website_logo = $setting->website_logo;
        $siteSettingEloquent->website_favicon = $setting->website_favicon;
        return $siteSettingEloquent;
    }
}
