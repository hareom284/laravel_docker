<?php

namespace Src\Company\Project\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Project\Domain\Repositories\PaymentTypeRepositoryInterface;

class DeletePaymentTypeCommand implements CommandInterface
{
    private PaymentTypeRepositoryInterface $repository;

    public function __construct(
        private readonly int $paymentTypeId
    )
    {
        $this->repository = app()->make(PaymentTypeRepositoryInterface::class);
    }

    public function execute()
    {
        return $this->repository->delete($this->paymentTypeId);
    }
}