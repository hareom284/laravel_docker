<?php

namespace Src\Company\Document\Domain\Repositories;
use Src\Company\Document\Domain\Model\Entities\Folder;
use Src\Company\Document\Application\DTO\FolderData;

interface FolderRepositoryInterface
{
    public function getFolders($filters = []);

    public function getFoldersByProjectId(int $projectId);

    public function findFolderById(int $id);

    public function store(Folder $folder): FolderData;

    public function update(Folder $folder): Folder;

    public function delete(int $folder_id): void;

}
