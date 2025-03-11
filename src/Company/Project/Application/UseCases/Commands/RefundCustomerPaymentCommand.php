<?php

namespace Src\Company\Project\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Project\Domain\Repositories\CustomerPaymentRepositoryInterface;

class RefundCustomerPaymentCommand implements CommandInterface
{
    private CustomerPaymentRepositoryInterface $repository;

    public function __construct(
        private readonly array $data,
        private readonly int $id,
    )
    {
        $this->repository = app()->make(CustomerPaymentRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->refundPayment($this->data,$this->id);
    }
}