<?php

namespace Src\Company\Document\Application\DTO;

use Illuminate\Http\Request;
use Src\Company\Document\Infrastructure\EloquentModels\PaymentTermEloquentModel;

class PaymentTermData
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly ?string $payment_terms,
        public readonly ?int $project_id,
        public readonly ?string $is_default

    )
    {}

    public static function fromRequest(Request $request, ?int $payment_term_id = null): PaymentTermData
    {
        return new self(
            id: $payment_term_id,
            name: $request->string('name'),
            payment_terms: $request->string('payment_terms'),
            project_id: $request->project_id,
            is_default: $request->is_default
        );
    }

    public static function fromEloquent(PaymentTermEloquentModel $paymentTermEloquentModel): self
    {
        return new self(
            id: $paymentTermEloquentModel->id,
            name: $paymentTermEloquentModel->name,
            payment_terms: $paymentTermEloquentModel->payment_terms,
            project_id: $paymentTermEloquentModel->project_id,
            is_default: $paymentTermEloquentModel->is_default
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'payment_terms' => $this->payment_terms,
            'project_id' => $this->project_id,
            'is_default' => $this->is_default
        ];
    }
}
