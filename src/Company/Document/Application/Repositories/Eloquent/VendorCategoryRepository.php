<?php

namespace Src\Company\Document\Application\Repositories\Eloquent;

use Illuminate\Support\Facades\DB;
use Src\Company\Document\Application\DTO\VendorCategoryData;
use Src\Company\Document\Application\Mappers\VendorCategoryMapper;
use Src\Company\Document\Domain\Model\Entities\VendorCategory;
use Src\Company\Document\Domain\Repositories\VendorCategoryRepositoryInterface;
use Src\Company\Document\Domain\Resources\VendorCategoryResource;
use Src\Company\Document\Infrastructure\EloquentModels\VendorCategoryEloquentModel;

class VendorCategoryRepository implements VendorCategoryRepositoryInterface
{

    public function index()
    {
        $vendorCategories = VendorCategoryEloquentModel::query()->orderBy('id', 'desc')->get();

        $finalResults = VendorCategoryResource::collection($vendorCategories);
        
        return $finalResults;
    }

    public function store(VendorCategory $vendorCategory): VendorCategoryData
    {
        return DB::transaction(function () use ($vendorCategory) {

            $vendorCategoryEloquent = VendorCategoryMapper::toEloquent($vendorCategory);

            $vendorCategoryEloquent->save();

            return VendorCategoryData::fromEloquent($vendorCategoryEloquent);
        });
    }

     public function update(VendorCategory $vendor): VendorCategoryData
    {
        $vendorCategoryEloquent = VendorCategoryMapper::toEloquent($vendor);

        $vendorCategoryEloquent->save();

        return VendorCategoryData::fromEloquent($vendorCategoryEloquent);
    }

    public function delete(int $vendorCategoryId): void
    {
        $vendorCategoryEloquent = VendorCategoryEloquentModel::query()->findOrFail($vendorCategoryId);
        $vendorCategoryEloquent->delete();
    }
}