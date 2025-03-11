<?php

namespace Src\Company\CompanyManagement\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\CompanyManagement\Domain\Repositories\FAQItemRepositoryInterface;

class DeleteFAQItemCommand implements CommandInterface
{
    private FAQItemRepositoryInterface $repository;

    public function __construct(
        private readonly int $id
    )
    {
        $this->repository = app()->make(FAQItemRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->delete($this->id);
    }
}