<?php

namespace Src\Company\Document\Application\Repositories\Eloquent;

use Illuminate\Support\Facades\DB;
use Src\Company\Document\Application\DTO\PurchaseOrderTemplateItemData;
use Src\Company\Document\Application\Mappers\PurchaseOrderTemplateItemMapper;
use Src\Company\Document\Domain\Model\Entities\PurchaseOrderTemplateItem;
use Src\Company\Document\Domain\Repositories\PurchaseOrderTemplateItemRepositoryInterface;
use Src\Company\Document\Domain\Resources\PurchaseOrderTemplateItemResource;
use Src\Company\Document\Infrastructure\EloquentModels\PurchaseOrderTemplateItemEloquentModel;

class PurchaseOrderTemplateItemRepository implements PurchaseOrderTemplateItemRepositoryInterface
{
    public function index($filters = [])
    {
        //purchase order item lists

        $perPage = $filters['perPage'] ?? 10;

        $query = PurchaseOrderTemplateItemEloquentModel::query();

        if(isset($filters['vendorCategoryId'])) {
            $vendorCategoryId = $filters['vendorCategoryId'];
            $query->where('vendor_category_id', $vendorCategoryId);
        }

        if(isset($filters['companyId'])){
            $companyId = $filters['companyId'];
            $query->where('company_id',$companyId);
        }

        $results = $query->orderBy('id', 'desc')->paginate($perPage);

        $links = [
            'first' => $results->url(1),
            'last' => $results->url($results->lastPage()),
            'prev' => $results->previousPageUrl(),
            'next' => $results->nextPageUrl(),
        ];

        $meta = [
            'current_page' => $results->currentPage(),
            'from' => $results->firstItem(),
            'last_page' => $results->lastPage(),
            'path' => $results->url($results->currentPage()),
            'per_page' => $perPage,
            'to' => $results->lastItem(),
            'total' => $results->total(),
        ];

        $finalResults = PurchaseOrderTemplateItemResource::collection($results);

        $responseData['data'] = $finalResults;
        $responseData['links'] = $links;
        $responseData['meta'] = $meta;

        return $responseData;
    }

    public function getItemsForPoCreate($companyId,$vendorCategoryId)
    {
        $poTemplateItems = PurchaseOrderTemplateItemEloquentModel::where('company_id',$companyId)
                            ->where('vendor_category_id',$vendorCategoryId)
                            ->get();

        $finalResults =  PurchaseOrderTemplateItemResource::collection($poTemplateItems);

        return $finalResults;
    }

    public function store(PurchaseOrderTemplateItem $purchaseOrderTemplateItem): PurchaseOrderTemplateItemData
    {        
        return DB::transaction(function () use ($purchaseOrderTemplateItem) {

            $purchaseOrderTemplateItemEloquent = PurchaseOrderTemplateItemMapper::toEloquent($purchaseOrderTemplateItem);

            $purchaseOrderTemplateItemEloquent->save();

            return PurchaseOrderTemplateItemData::fromEloquent($purchaseOrderTemplateItemEloquent);
        });
    }

    public function update(PurchaseOrderTemplateItem $purchaseOrderTemplateItem): PurchaseOrderTemplateItemData
    {        
        $poTemplateItemEloquent = PurchaseOrderTemplateItemMapper::toEloquent($purchaseOrderTemplateItem);

        $poTemplateItemEloquent->save();

        return PurchaseOrderTemplateItemData::fromEloquent($poTemplateItemEloquent);
    }

    public function delete(int $id): void
    {
        $poTemplateItemEloquent = PurchaseOrderTemplateItemEloquentModel::query()->findOrFail($id);

        $poTemplateItemEloquent->delete();
    }

}