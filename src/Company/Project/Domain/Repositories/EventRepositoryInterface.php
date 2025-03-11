<?php

namespace Src\Company\Project\Domain\Repositories;
use Src\Company\Project\Domain\Model\Entities\Event;
use Src\Company\Project\Application\DTO\EventData;

interface EventRepositoryInterface
{
    public function getEvents();

    public function getEventsByProjectId(int $projectId);

    public function getEventsByGroup();

    public function findById(int $id): EventData;

    public function store(Event $event,$eventComments): EventData;

    public function update(Event $event,$eventComments): EventData;

    public function changeStatus(int $id,string $status): EventData;

    public function eventCommentsByEventId(int $id);

    public function delete(int $event): void;

}
