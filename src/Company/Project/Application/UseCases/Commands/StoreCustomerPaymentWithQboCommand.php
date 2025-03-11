<?php

namespace Src\Company\Project\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Project\Domain\Model\Entities\CustomerPayment;
use Src\Company\Project\Domain\Repositories\CustomerPaymentRepositoryInterface;

class StoreCustomerPaymentWithQboCommand implements CommandInterface
{
    private CustomerPaymentRepositoryInterface $repository;

    public function __construct(
        private readonly int $projectId,
    )
    {
        $this->repository = app()->make(CustomerPaymentRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->importFromQbo($this->projectId);
    }
}