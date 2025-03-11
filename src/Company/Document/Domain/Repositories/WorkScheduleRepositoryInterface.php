<?php

namespace Src\Company\Document\Domain\Repositories;

interface WorkScheduleRepositoryInterface
{

    public function getWorkSchedules(int $projectId);

    public function show($id);

    public function store($projectId, $documentFiles);

    public function delete($id);

}
