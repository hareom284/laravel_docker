<?php

namespace Src\Company\Project\Application\DTO;

use Illuminate\Http\Request;
use Src\Company\Project\Infrastructure\EloquentModels\PaymentTypeEloquentModel;

class PaymentTypeData
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,

    )
    {}

    public static function fromRequest(Request $request, ?int $payment_term_id = null): PaymentTypeData
    {
        return new self(
            id: $payment_term_id,
            name: $request->string('name'),
        );
    }

    public static function fromEloquent(PaymentTypeEloquentModel $paymentTermEloquentModel): self
    {
        return new self(
            id: $paymentTermEloquentModel->id,
            name: $paymentTermEloquentModel->name,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}
