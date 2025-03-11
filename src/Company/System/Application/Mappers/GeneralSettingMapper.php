<?php

namespace Src\Company\System\Application\Mappers;

use Illuminate\Http\Request;
use Src\Company\System\Domain\Model\Entities\GeneralSetting;
use Src\Company\System\Infrastructure\EloquentModels\GeneralSettingEloquentModel;
use Illuminate\Support\Facades\Storage;

class GeneralSettingMapper
{
    public static function fromRequest(Request $request): array
    {
        $generalSettings = [];

        // Decode the JSON string to array

        $settingsValues = $request->input('settingsValues');

        if (is_string($settingsValues)) {
            $settingsValues = json_decode($request->input('settingsValues'), true);
            foreach ($settingsValues as $setting) {
                $generalSettings[] = new GeneralSetting(
                    setting: $setting['setting'],
                    value: $setting['value'],
                    is_array: false
                );
            }

        } else {
            $settingsValues = $request->input('settingsValues');
            foreach ($settingsValues as $setting) {
                $generalSettings[] = [
                    "setting" => $setting['setting'],
                    "value" => $setting['value']
                ];
            }

        }
        return $generalSettings;
    }

    public static function fromEloquent(GeneralSettingEloquentModel $generalSettingEloquent): GeneralSetting
    {
        return new GeneralSetting(
            setting: $generalSettingEloquent->setting,
            value: $generalSettingEloquent->value,
            is_array: $generalSettingEloquent->is_array

        );
    }

    public static function toEloquent(GeneralSetting $setting): GeneralSettingEloquentModel
    {
        $generalSettingEloquent = new GeneralSettingEloquentModel();
        $generalSettingEloquent->setting = $setting->setting;
        $generalSettingEloquent->value = $setting->value;
        $generalSettingEloquent->is_array = $setting->is_array;
        return $generalSettingEloquent;
    }
}
