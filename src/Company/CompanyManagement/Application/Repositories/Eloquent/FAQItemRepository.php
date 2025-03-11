<?php

namespace Src\Company\CompanyManagement\Application\Repositories\Eloquent;

use Illuminate\Support\Facades\DB;
use Src\Company\CompanyManagement\Application\DTO\FAQItemData;
use Src\Company\CompanyManagement\Application\Mappers\FAQItemMapper;
use Src\Company\CompanyManagement\Domain\Model\Entities\FAQItem;
use Src\Company\CompanyManagement\Domain\Resources\FAQItemResource;
use Src\Company\CompanyManagement\Domain\Repositories\FAQItemRepositoryInterface;
use Src\Company\CompanyManagement\Infrastructure\EloquentModels\FAQItemEloquentModel;

class FAQItemRepository implements FAQItemRepositoryInterface
{

    public function getFAQItems($filters = [])
    {

        $perPage = $filters['perPage'] ?? 10;

        $roles = auth('sanctum')->user()->roles;

        $roleArray = [];

        foreach ($roles as $key => $value) {
            array_push($roleArray,$value->name);
        }

        if(in_array('Customer',$roleArray)){
            $userId = auth('sanctum')->user()->id;

            $faqEloquent = FAQItemEloquentModel::where('customer_id',$userId)->orWhere('customer_id',null)->filter($filters)->orderBy('id', 'desc')->paginate($perPage);

        } else {
            $faqEloquent = FAQItemEloquentModel::filter($filters)->orderBy('id', 'desc')->paginate($perPage);
        }

        // $faqEloquent = FAQItemEloquentModel::filter($filters)->orderBy('id', 'desc')->paginate($perPage);

        $faqItems = FAQItemResource::collection($faqEloquent);

        $links = [
            'first' => $faqItems->url(1),
            'last' => $faqItems->url($faqItems->lastPage()),
            'prev' => $faqItems->previousPageUrl(),
            'next' => $faqItems->nextPageUrl(),
        ];
        $meta = [
            'current_page' => $faqItems->currentPage(),
            'from' => $faqItems->firstItem(),
            'last_page' => $faqItems->lastPage(),
            'path' => $faqItems->url($faqItems->currentPage()),
            'per_page' => $perPage,
            'to' => $faqItems->lastItem(),
            'total' => $faqItems->total(),
        ];
        $responseData['data'] = $faqItems;
        $responseData['links'] = $links;
        $responseData['meta'] = $meta;
        
        return $faqEloquent;
    }

    public function getCustomerFAQ()
    {
        $faqEloquent = FAQItemEloquentModel::query()->with('project.property','customer')->whereNotNull('project_id')->whereNotNull('customer_id')->get();

        $faqItems = FAQItemResource::collection($faqEloquent);

        return $faqItems;

    }

    public function replyCustomerFAQ(int $id,?string $answer = null)
    {
        $faqEloquent = FAQItemEloquentModel::query()->where('id',$id)->update([
            'answer' => $answer,
            'status' => 2
        ]);

        return $faqEloquent;
    }

    public function findFAQItemById(int $id): FAQItemData
    {
        $faqEloquent = FAQItemEloquentModel::query()->findOrFail($id);

        return FAQItemData::fromEloquent($faqEloquent, true, true, true);
    }

    public function store(FAQItem $faq): FAQItemData
    {
        return DB::transaction(function () use ($faq) {

            $faqEloquent = FAQItemMapper::toEloquent($faq);

            $faqEloquent->save();

            return FAQItemData::fromEloquent($faqEloquent);
        });
    }

    public function update(FAQItem $faq): FAQItem
    {
        $faqArray = $faq->toArray();

        $faqEloquent = FAQItemEloquentModel::query()->findOrFail($faq->id);

        $faqEloquent->fill($faqArray);

        $faqEloquent->save();
        
        return $faq;
    }

    public function delete(int $faq_id): void
    {
        $faqEloquent = FAQItemEloquentModel::query()->findOrFail($faq_id);
        $faqEloquent->delete();
    }
}