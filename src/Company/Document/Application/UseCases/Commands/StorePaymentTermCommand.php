<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Model\Entities\PaymentTerm;
use Src\Company\Document\Domain\Repositories\PaymentTermRepositoryInterface;

class StorePaymentTermCommand implements CommandInterface
{
    private PaymentTermRepositoryInterface $repository;

    public function __construct(
        private readonly PaymentTerm $paymentTerm,

    )
    {
        $this->repository = app()->make(PaymentTermRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->store($this->paymentTerm);
    }
}