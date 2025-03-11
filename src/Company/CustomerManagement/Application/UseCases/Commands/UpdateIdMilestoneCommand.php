<?php

namespace Src\Company\CustomerManagement\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\CustomerManagement\Domain\Repositories\CustomerRepositoryInterface;

class UpdateIdMilestoneCommand implements CommandInterface
{
    private CustomerRepositoryInterface $repository;

    public function __construct(
        private readonly array $data,
    )
    {
        $this->repository = app()->make(CustomerRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->updateIdMilestone($this->data);
    }
}
