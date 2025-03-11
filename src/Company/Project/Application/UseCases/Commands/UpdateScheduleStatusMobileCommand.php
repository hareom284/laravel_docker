<?php

namespace Src\Company\Project\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Project\Domain\Repositories\RenovationItemScheduleMobileInterface;

class UpdateScheduleStatusMobileCommand implements CommandInterface
{
    private RenovationItemScheduleMobileInterface $repository;

    public function __construct(
        private readonly array $scheduleArray,
        private readonly int $id
    )
    {
        $this->repository = app()->make(RenovationItemScheduleMobileInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->updateStatus($this->scheduleArray,$this->id);
    }
}