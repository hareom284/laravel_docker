<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Repositories\WorkScheduleRepositoryInterface;

class DeleteWorkScheduleCommand implements CommandInterface
{
    private WorkScheduleRepositoryInterface $repository;

    public function __construct(
        private readonly string $id,
    )
    {
        $this->repository = app()->make(WorkScheduleRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->delete($this->id);
    }
}