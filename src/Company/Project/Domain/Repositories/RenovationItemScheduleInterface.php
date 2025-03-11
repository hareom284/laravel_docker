<?php

namespace Src\Company\Project\Domain\Repositories;

interface RenovationItemScheduleInterface
{
    public function index(int $projectId);

    public function getEvoItemSchedule(int $projectId);

    public function getDates(int $projectId);

    public function getRenoItemCount(int $projectId);

    public function store(int $documentId);

    public function updateSchedule($scheduleArray);

    public function updateStatus($scheduleArray,$id);

    public function updateAllStatus(array $itemsIds,$isChecked);

    public function updateEvoItemsStatus(int $Itemid,int $roomId,int $status);

    public function updateAllEvoItemsStatus(int $evoId,int $status);

    public function updateEvoSchedule(array $scheduleArray);
}