<?php

namespace Src\Company\Project\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Project\Domain\Repositories\CustomerPaymentRepositoryInterface;

class DeleteCustomerPaymentCommand implements CommandInterface
{
    private CustomerPaymentRepositoryInterface $repository;

    public function __construct(
        private readonly int $customer_payment_id
    )
    {
        $this->repository = app()->make(CustomerPaymentRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->destroy($this->customer_payment_id);
    }
}