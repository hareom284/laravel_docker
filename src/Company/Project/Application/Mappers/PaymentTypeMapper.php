<?php

namespace Src\Company\Project\Application\Mappers;
use Illuminate\Http\Request;
use Src\Company\Project\Domain\Model\Entities\PaymentType;
use Src\Company\Project\Infrastructure\EloquentModels\PaymentTypeEloquentModel;

class PaymentTypeMapper
{
    public static function fromRequest(Request $request, ?int $payment_type_id = null): PaymentType
    {
        return new PaymentType(
            id: $payment_type_id,
            name: $request->string('name'),
        );
    }

    public static function fromEloquent(PaymentTypeEloquentModel $teamEloquent): PaymentType
    {
        return new PaymentType(
            id: $teamEloquent->id,
            name:$teamEloquent->name,
        );
    }

    public static function toEloquent(PaymentType $paymentType): PaymentTypeEloquentModel
    {
        $paymentTypeEloquent = new PaymentTypeEloquentModel();
        if ($paymentType->id) {
            $paymentTypeEloquent = PaymentTypeEloquentModel::query()->findOrFail($paymentType->id);
        }
        $paymentTypeEloquent->name = $paymentType->name;
        return $paymentTypeEloquent;
    }
}
