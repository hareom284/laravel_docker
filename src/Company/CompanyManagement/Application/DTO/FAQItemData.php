<?php

namespace Src\Company\CompanyManagement\Application\DTO;

use Illuminate\Http\Request;
use Src\Company\CompanyManagement\Infrastructure\EloquentModels\FAQItemEloquentModel;

class FAQItemData
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $question,
        public readonly ?string $answer,
        public readonly ?int $project_id,
        public readonly ?int $customer_id,
        public readonly int $status
    )
    {}

    public static function fromRequest(Request $request, ?int $faq_id = null): FAQItemData
    {
        return new self(
            id: $faq_id,
            question: $request->string('question'),
            answer: $request->string('answer'),
            project_id: $request->integer('project_id'),
            customer_id: $request->integer('customer_id'),
            status: $request->integer('status')
        );
    }

    public static function fromEloquent(FAQItemEloquentModel $faqEloquent): self
    {
        return new self(
            id: $faqEloquent->id,
            question: $faqEloquent->question,
            answer: $faqEloquent->answer,
            project_id: $faqEloquent->project_id,
            customer_id: $faqEloquent->customer_id,
            status: $faqEloquent->status
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'question' => $this->question,
            'answer' => $this->answer,
            'project_id' => $this->project_id,
            'customer_id' => $this->customer_id,
            'status' => $this->status
        ];
    }
}