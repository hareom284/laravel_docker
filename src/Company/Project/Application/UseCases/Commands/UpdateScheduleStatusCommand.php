<?php

namespace Src\Company\Project\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Project\Domain\Repositories\RenovationItemScheduleInterface;

class UpdateScheduleStatusCommand implements CommandInterface
{
    private RenovationItemScheduleInterface $repository;

    public function __construct(
        private readonly array $scheduleArray,
        private readonly int $id
    )
    {
        $this->repository = app()->make(RenovationItemScheduleInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->updateStatus($this->scheduleArray,$this->id);
    }
}