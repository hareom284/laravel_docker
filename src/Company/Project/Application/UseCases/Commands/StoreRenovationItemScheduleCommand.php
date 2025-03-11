<?php

namespace Src\Company\Project\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Project\Domain\Model\Entities\RenovationItemSchedule;
use Src\Company\Project\Domain\Repositories\RenovationItemScheduleInterface;

class StoreRenovationItemScheduleCommand implements CommandInterface
{
    private RenovationItemScheduleInterface $repository;

    public function __construct(
        private readonly RenovationItemSchedule $schedule
    )
    {
        $this->repository = app()->make(RenovationItemScheduleInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->store($this->schedule);
    }
}