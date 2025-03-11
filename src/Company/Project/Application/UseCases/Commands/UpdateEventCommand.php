<?php

namespace Src\Company\Project\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Project\Domain\Model\Entities\Event;
use Src\Company\Project\Domain\Policies\ProjectPolicy;
use Src\Company\Project\Domain\Repositories\EventRepositoryInterface;

class UpdateEventCommand implements CommandInterface
{
    private EventRepositoryInterface $repository;

    public function __construct(
        private readonly Event $event,
        private readonly array $eventComments
    )
    {
        $this->repository = app()->make(EventRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('updateEvent', ProjectPolicy::class);
        return $this->repository->update($this->event,$this->eventComments);
    }
}