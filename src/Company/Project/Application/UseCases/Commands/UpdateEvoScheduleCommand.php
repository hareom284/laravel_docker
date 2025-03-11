<?php

namespace Src\Company\Project\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Project\Domain\Repositories\RenovationItemScheduleInterface;

class UpdateEvoScheduleCommand implements CommandInterface
{
    private RenovationItemScheduleInterface $repository;

    public function __construct(
        private readonly array $scheduleArray
    )
    {
        $this->repository = app()->make(RenovationItemScheduleInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->updateEvoSchedule($this->scheduleArray);
    }
}