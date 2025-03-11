<?php

namespace Src\Company\Project\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Project\Domain\Repositories\RenovationItemScheduleMobileInterface;

class UpdateScheduleMobileCommand implements CommandInterface
{
    private RenovationItemScheduleMobileInterface $repository;

    public function __construct(
        private readonly array $scheduleArray
    )
    {
        $this->repository = app()->make(RenovationItemScheduleMobileInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->updateSchedule($this->scheduleArray);
    }
}