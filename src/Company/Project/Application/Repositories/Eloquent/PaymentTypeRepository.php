<?php

namespace Src\Company\Project\Application\Repositories\Eloquent;

use Src\Company\Project\Application\DTO\PaymentTypeData;
use Src\Company\Project\Application\Mappers\PaymentTypeMapper;
use Src\Company\Project\Domain\Model\Entities\PaymentType;
use Src\Company\Project\Domain\Repositories\PaymentTypeRepositoryInterface;
use Src\Company\Project\Infrastructure\EloquentModels\PaymentTypeEloquentModel;

class PaymentTypeRepository implements PaymentTypeRepositoryInterface
{
    public function index($filters = [])
    {
        $paymentTypeEloquent = PaymentTypeEloquentModel::query();
        return $paymentTypeEloquent->get();

    }

    public function findPaymentTypeById(int $id)
    {
        $paymentTypeEloquent = PaymentTypeEloquentModel::query();

        return $paymentTypeEloquent->findOrFail($id);
    }

    public function store(PaymentType $paymentType): PaymentTypeData
    {

        $paymentType = PaymentTypeMapper::toEloquent($paymentType);
        $paymentType->save();
        return PaymentTypeData::fromEloquent($paymentType);
    }

    public function update(PaymentType $paymentType): PaymentTypeData
    {
        $paymentTypeEloquent = PaymentTypeMapper::toEloquent($paymentType);

        $paymentTypeEloquent->save();

        return PaymentTypeData::fromEloquent($paymentTypeEloquent);
    }

    public function delete(int $paymentTypeId): void
    {
        $paymentTypeEloquent = PaymentTypeEloquentModel::query()->findOrFail($paymentTypeId);
        $paymentTypeEloquent->delete();
    }
}
