<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Repositories\PaymentTermRepositoryInterface;

class DeletePaymentTermCommand implements CommandInterface
{
    private PaymentTermRepositoryInterface $repository;

    public function __construct(
        private readonly int $paymentTermId
    )
    {
        $this->repository = app()->make(PaymentTermRepositoryInterface::class);
    }

    public function execute()
    {
        return $this->repository->delete($this->paymentTermId);
    }
}