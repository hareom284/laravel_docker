<?php

namespace Src\Company\Project\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Project\Domain\Model\Entities\PaymentType;
use Src\Company\Project\Domain\Repositories\PaymentTypeRepositoryInterface;

class UpdatePaymentTypeCommand implements CommandInterface
{
    private PaymentTypeRepositoryInterface $repository;

    public function __construct(
        private readonly PaymentType $paymentType
    )
    {
        $this->repository = app()->make(PaymentTypeRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->update($this->paymentType);
    }
}