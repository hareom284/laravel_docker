<?php

namespace Src\Company\CustomerManagement\Application\Repositories\Eloquent;

use Illuminate\Support\Facades\Storage;
use Src\Company\CustomerManagement\Application\DTO\ReferrerFormData;
use Src\Company\CustomerManagement\Application\Mappers\ReferrerFormMapper;
use Src\Company\CustomerManagement\Domain\Model\Entities\ReferrerForm;
use Src\Company\CustomerManagement\Domain\Repositories\ReferrerFormRepositoryInterface;
use Src\Company\CustomerManagement\Domain\Resources\ReferrerFormResource;
use Src\Company\CustomerManagement\Infrastructure\EloquentModels\ReferrerFormEloquentModel;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;

class ReferrerFormRepository implements ReferrerFormRepositoryInterface
{

    public function findAllReferrerFormsQuery($filters = [])
    {
        //user lists
        logger([$filters,'filters']);
        $perPage = $filters['perPage'] ?? 10;

        $referrerFormEloquent = ReferrerFormEloquentModel::filter($filters)->orderBy('id', 'desc')->paginate($perPage);

        $referrerForms = ReferrerFormResource::collection($referrerFormEloquent);

        $links = [
            'first' => $referrerForms->url(1),
            'last' => $referrerForms->url($referrerForms->lastPage()),
            'prev' => $referrerForms->previousPageUrl(),
            'next' => $referrerForms->nextPageUrl(),
        ];
        $meta = [
            'current_page' => $referrerForms->currentPage(),
            'from' => $referrerForms->firstItem(),
            'last_page' => $referrerForms->lastPage(),
            'path' => $referrerForms->url($referrerForms->currentPage()),
            'per_page' => $perPage,
            'to' => $referrerForms->lastItem(),
            'total' => $referrerForms->total(),
        ];
        $responseData['data'] = $referrerForms;
        $responseData['links'] = $links;
        $responseData['meta'] = $meta;

        return $responseData;
    }

    public function findReferrerForm($id)
    {
        $referrerForm = ReferrerFormEloquentModel::where('owner_id',$id)->first();
        if($referrerForm){
            return new ReferrerFormResource($referrerForm);
        }
    }

    public function store(ReferrerForm $referrerForm): ReferrerFormData
    {
        $referrerFormEloquent = ReferrerFormMapper::toEloquent($referrerForm);

        $referrerFormEloquent->save();

        if($referrerForm->owner_id && $referrerForm->relation_with_referrer){
            $owner = UserEloquentModel::find($referrerForm->owner_id);
            $owner->update([
                'relation_with_referrer' => $referrerForm->relation_with_referrer
            ]);
        }

        return ReferrerFormData::fromEloquent($referrerFormEloquent);
    }

    public function sign(ReferrerForm $referrerForm): ReferrerFormData
    {
        $referrerFormEloquent = ReferrerFormMapper::toEloquent($referrerForm);
        $referrerFormEloquent->save();

        if($referrerForm->owner_id && $referrerForm->relation_with_referrer){
            $owner = UserEloquentModel::find($referrerForm->owner_id);
            $owner->update([
                'relation_with_referrer' => $referrerForm->relation_with_referrer
            ]);
        }

        if($referrerForm->management_signature && $referrerForm->referrer_id){
            $referrerForm = UserEloquentModel::find($referrerForm->referrer_id);
            $referrerForm->update([
                'is_referrer'=>true
            ]);
        }

        return ReferrerFormData::fromEloquent($referrerFormEloquent);
    }


    public function downloadReferrerForm(int $referrerFormId): void
    {
        $referrerFormEloquent = ReferrerFormEloquentModel::query()->findOrFail($referrerFormId);
    }

    public function findApprovedReferrers()
    {
        $referrers = UserEloquentModel::where('is_referrer','=',1)->select('first_name','last_name','id')->get();
        return $referrers;
    }
}
