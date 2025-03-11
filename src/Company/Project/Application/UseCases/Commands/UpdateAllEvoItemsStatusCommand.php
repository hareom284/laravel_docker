<?php

namespace Src\Company\Project\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Project\Domain\Repositories\RenovationItemScheduleInterface;

class UpdateAllEvoItemsStatusCommand implements CommandInterface
{
    private RenovationItemScheduleInterface $repository;

    public function __construct(
        private readonly int $evoId,
        private readonly int $status
    )
    {
        $this->repository = app()->make(RenovationItemScheduleInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->updateAllEvoItemsStatus($this->evoId,$this->status);
    }
}