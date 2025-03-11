<?php

namespace Src\Company\Project\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Project\Domain\Repositories\RenovationItemScheduleInterface;

class FindRenovationItemCountWithSectionQuery implements QueryInterface
{
    private RenovationItemScheduleInterface $repository;

    public function __construct(
        private readonly int $projectId
    )
    {
        $this->repository = app()->make(RenovationItemScheduleInterface::class);
    }

    public function handle()
    {
        return $this->repository->getRenoItemCount($this->projectId);
    }
}