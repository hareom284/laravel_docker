<?php

namespace Src\Company\Project\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Project\Domain\Repositories\RenovationItemScheduleMobileInterface;

class FindAllRenovationItemSchedulesByDateMobileQuery implements QueryInterface
{
    private RenovationItemScheduleMobileInterface $repository;

    public function __construct(
        private readonly int $projectId,
        private readonly string $date
    )
    {
        $this->repository = app()->make(RenovationItemScheduleMobileInterface::class);
    }

    public function handle()
    {
        return $this->repository->getSectionsByDate($this->projectId, $this->date);
    }
}