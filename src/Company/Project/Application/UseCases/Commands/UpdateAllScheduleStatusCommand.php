<?php

namespace Src\Company\Project\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Project\Domain\Repositories\RenovationItemScheduleInterface;

class UpdateAllScheduleStatusCommand implements CommandInterface
{
    private RenovationItemScheduleInterface $repository;

    public function __construct(private readonly array $itemsIds,private readonly int $isChecked)
    {
        $this->repository = app()->make(RenovationItemScheduleInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->updateAllStatus($this->itemsIds,$this->isChecked);
    }
}