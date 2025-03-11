<?php

namespace Src\Company\CompanyManagement\Application\Mappers;

use Illuminate\Http\Request;
use Src\Company\CompanyManagement\Domain\Model\Entities\FAQItem;
use Src\Company\CompanyManagement\Infrastructure\EloquentModels\FAQItemEloquentModel;

class FAQItemMapper
{
    public static function fromRequest(Request $request, ?int $faq_id = null): FAQItem
    {
        return new FAQItem(
            id: $faq_id,
            question: $request->string('question'),
            answer: $request->string('answer') ?? null,
            project_id: $request->integer('project_id') ?? null,
            customer_id: $request->integer('customer_id') ?? null,
            status: $request->integer('status') ?? 2
        );
    }

    public static function fromEloquent(FAQItemEloquentModel $faqEloquent): FAQItem
    {
        return new FAQItem(
            id: $faqEloquent->id,
            question: $faqEloquent->question,
            answer: $faqEloquent->answer,
            project_id: $faqEloquent->project_id,
            customer_id: $faqEloquent->customer_id,
            status: $faqEloquent->status,
        );
    }

    public static function toEloquent(FAQItem $faq): FAQItemEloquentModel
    {
        $faqEloquent = new FAQItemEloquentModel();
        if ($faq->id) {
            $faqEloquent = FAQItemEloquentModel::query()->findOrFail($faq->id);
        }
        $faqEloquent->question = $faq->question;
        $faqEloquent->answer = $faq->answer;
        $faqEloquent->project_id = $faq->project_id;
        $faqEloquent->customer_id = $faq->customer_id;
        $faqEloquent->status = $faq->status;
        return $faqEloquent;
    }
}
