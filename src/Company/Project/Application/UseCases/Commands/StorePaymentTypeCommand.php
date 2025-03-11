<?php

namespace Src\Company\Project\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Project\Domain\Model\Entities\PaymentType;
use Src\Company\Project\Domain\Repositories\PaymentTypeRepositoryInterface;

class StorePaymentTypeCommand implements CommandInterface
{
    private PaymentTypeRepositoryInterface $repository;

    public function __construct(
        private readonly PaymentType $paymentTerm,

    )
    {
        $this->repository = app()->make(PaymentTypeRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->store($this->paymentTerm);
    }
}