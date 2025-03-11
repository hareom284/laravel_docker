<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Domain\Repositories\WorkScheduleRepositoryInterface;

class FindWorkScheduleByIdQuery implements QueryInterface
{
    private WorkScheduleRepositoryInterface $repository;

    public function __construct(
        private readonly string $id,
    )
    {
        $this->repository = app()->make(WorkScheduleRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->show($this->id);
    }
}