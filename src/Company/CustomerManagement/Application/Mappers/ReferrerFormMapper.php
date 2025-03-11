<?php

namespace Src\Company\CustomerManagement\Application\Mappers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Src\Company\CustomerManagement\Domain\Model\Entities\ReferrerForm;
use Src\Company\CustomerManagement\Infrastructure\EloquentModels\ReferrerFormEloquentModel;

class ReferrerFormMapper
{
    public static function fromRequest(Request $request, ?int $referrer_form_id = null): ReferrerForm
    {

        $owner_signature = self::uploadFile($request->file('owner_signature'));
        $salesperson_signature = self::uploadFile($request->file('salesperson_signature'));
        $management_signature = self::uploadFile($request->file('management_signature'));
        return new ReferrerForm(
            id: $referrer_form_id,
            owner_id: $request->integer('owner_id'),
            referrer_id: $request->referrer_id ? $request->integer('referrer_id') : null,
            referrer_properties: $request->string('referrer_properties'),
            signed_by_salesperson_id: $request->signed_by_salesperson_id ?  $request->integer('signed_by_salesperson_id') : null,
            signed_by_management_id: $request->signed_by_management_id ? $request->integer('signed_by_management_id') : null,
            owner_signature: $owner_signature ? $owner_signature : $request->owner_signature,
            salesperson_signature: $salesperson_signature ? $salesperson_signature : $request->salesperson_signature,
            management_signature: $management_signature ? $management_signature : $request->management_signature,
            date_of_referral: $request->string('date_of_referral'),
            relation_with_referrer: $request->string('relation_with_referrer')
        );
    }

    public static function fromEloquent(ReferrerFormEloquentModel $referrerFormEloquent): ReferrerForm
    {
        return new ReferrerForm(
            id: $referrerFormEloquent->id,
            owner_id: $referrerFormEloquent->owner_id,
            referrer_id: $referrerFormEloquent->referrer_id,
            referrer_properties: $referrerFormEloquent->referrer_properties,
            signed_by_salesperson_id: $referrerFormEloquent->signed_by_salesperson_id,
            signed_by_management_id: $referrerFormEloquent->signed_by_management_id,
            owner_signature: $referrerFormEloquent->owner_signature,
            salesperson_signature: $referrerFormEloquent->salesperson_signature,
            management_signature: $referrerFormEloquent->management_signature,
            date_of_referral: $referrerFormEloquent->date_of_referral,
            relation_with_referrer: $referrerFormEloquent->relation_with_referrer
        );
    }

    public static function toEloquent(ReferrerForm $referrer_form): ReferrerFormEloquentModel
    {
        $referrerFormEloquent = new ReferrerFormEloquentModel();
        if ($referrer_form->id) {
            $referrerFormEloquent = ReferrerFormEloquentModel::query()->findOrFail($referrer_form->id);
        }
       
        $referrerFormEloquent->owner_id = $referrer_form->owner_id;
        $referrerFormEloquent->referrer_id = $referrer_form->referrer_id ?? null;
        $referrerFormEloquent->referrer_properties = $referrer_form->referrer_properties;
        $referrerFormEloquent->signed_by_salesperson_id = $referrer_form->signed_by_salesperson_id ?? null;
        $referrerFormEloquent->signed_by_management_id = $referrer_form->signed_by_management_id ?? null;
        $referrerFormEloquent->owner_signature = $referrer_form->owner_signature;
        $referrerFormEloquent->salesperson_signature = $referrer_form->salesperson_signature;
        $referrerFormEloquent->management_signature = $referrer_form->management_signature;
        $referrerFormEloquent->date_of_referral = $referrer_form->date_of_referral;
        return $referrerFormEloquent;
    }

    public static function uploadFile($file, $directory = 'referrer_form')
    {
        if ($file) {
            $uniqueFileName = uniqid() . '.' . $file->extension();
            $filePath = $directory . '/' . $uniqueFileName;
            Storage::disk('public')->put($filePath, file_get_contents($file));
            return $uniqueFileName;
        }
        return null;
    }
}
