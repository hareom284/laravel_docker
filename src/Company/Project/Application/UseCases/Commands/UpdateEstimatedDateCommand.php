<?php

namespace Src\Company\Project\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Project\Domain\Repositories\CustomerPaymentRepositoryInterface;

class UpdateEstimatedDateCommand implements CommandInterface
{
    private CustomerPaymentRepositoryInterface $repository;

    public function __construct(
        private readonly ?string $customer_payments
    )
    {
        $this->repository = app()->make(CustomerPaymentRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->updateEstimatedDate($this->customer_payments);
    }
}