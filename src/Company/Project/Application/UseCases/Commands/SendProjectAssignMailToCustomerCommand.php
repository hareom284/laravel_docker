<?php

namespace Src\Company\Project\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Project\Application\DTO\ProjectData;
use Src\Company\Project\Domain\Repositories\ProjectRepositoryInterface;

class SendProjectAssignMailToCustomerCommand implements CommandInterface
{
    private ProjectRepositoryInterface $repository;

    public function __construct(
        private readonly int $projectId,
        private readonly string $customerName,
        private readonly string $customerEmail,
        private readonly string $customerPassword
    )
    {
        $this->repository = app()->make(ProjectRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->sendMailToCustomer($this->projectId,$this->customerName,$this->customerEmail,$this->customerPassword);
    }
}