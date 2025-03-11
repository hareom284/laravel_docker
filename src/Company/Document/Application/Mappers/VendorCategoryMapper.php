<?php

namespace Src\Company\Document\Application\Mappers;
use Illuminate\Http\Request;
use Src\Company\Document\Domain\Model\Entities\VendorCategory;
use Src\Company\Document\Infrastructure\EloquentModels\VendorCategoryEloquentModel;

class VendorCategoryMapper
{
    public static function fromRequest(Request $request, ?int $vendor_category_id = null): VendorCategory
    {
        return new VendorCategory(
            id: $vendor_category_id,
            type: $request->string('type')
        );
    }

    public static function fromEloquent(VendorCategoryEloquentModel $vendorCategoryEloquent): VendorCategory
    {
        return new VendorCategory(
            id: $vendorCategoryEloquent->id,
            type: $vendorCategoryEloquent->type,
        );
    }

    public static function toEloquent(VendorCategory $vendorCategory): VendorCategoryEloquentModel
    {
        $vendorCategoryEloquent = new VendorCategoryEloquentModel();

        if ($vendorCategory->id) {
            $vendorCategoryEloquent = VendorCategoryEloquentModel::query()->findOrFail($vendorCategory->id);
        }

        $vendorCategoryEloquent->type = $vendorCategory->type;
        
        return $vendorCategoryEloquent;
    }
}