<?php

namespace Src\Company\Project\Domain\Repositories;

interface RenovationItemScheduleMobileInterface
{
    public function store(int $documentId);

    public function getDates(int $projectId);

    public function getRenoItemCount(int $projectId);

    public function index(int $projectId);

    public function updateSchedule($scheduleArray);

    public function updateStatus($scheduleArray,$id);

    public function getSectionsByDate(int $projectId, string $date);
}