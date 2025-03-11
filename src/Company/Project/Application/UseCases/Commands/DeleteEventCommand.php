<?php

namespace Src\Company\Project\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Project\Domain\Model\Entities\Event;
use Src\Company\Project\Domain\Policies\ProjectPolicy;
use Src\Company\Project\Domain\Repositories\EventRepositoryInterface;

class DeleteEventCommand implements CommandInterface
{
    private EventRepositoryInterface $repository;

    public function __construct(
        private readonly int $event_id
    )
    {
        $this->repository = app()->make(EventRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('deleteEvent', ProjectPolicy::class);
        return $this->repository->delete($this->event_id);
    }
}