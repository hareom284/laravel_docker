<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Model\Entities\Folder;
use Src\Company\Document\Domain\Policies\DocumentPolicy;
use Src\Company\Document\Domain\Repositories\FolderRepositoryInterface;

class DeleteFolderCommand implements CommandInterface
{
    private FolderRepositoryInterface $repository;

    public function __construct(
        private readonly int $folder_id
    )
    {
        $this->repository = app()->make(FolderRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('deleteFolder', DocumentPolicy::class);
        return $this->repository->delete($this->folder_id);
    }
}