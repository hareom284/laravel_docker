<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Model\Entities\Folder;
use Src\Company\Document\Domain\Policies\DocumentPolicy;
use Src\Company\Document\Domain\Repositories\FolderRepositoryInterface;

class UpdateFolderCommand implements CommandInterface
{
    private FolderRepositoryInterface $repository;

    public function __construct(
        private readonly Folder $folder
    )
    {
        $this->repository = app()->make(FolderRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('updateFolder', DocumentPolicy::class);
        return $this->repository->update($this->folder);
    }
}