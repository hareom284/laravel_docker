<?php

namespace Src\Company\Project\Domain\Repositories;

use Src\Company\Project\Application\DTO\PaymentTypeData;
use Src\Company\Project\Domain\Model\Entities\PaymentType;

interface PaymentTypeRepositoryInterface
{
    public function index();

    public function findPaymentTypeById(int $id);

    public function store(PaymentType $paymentType): PaymentTypeData;

    public function update(PaymentType $paymentType): PaymentTypeData;

    public function delete(int $paymentTypeId);

}
