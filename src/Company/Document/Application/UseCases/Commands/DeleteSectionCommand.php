<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Model\Entities\Folder;
use Src\Company\Document\Domain\Policies\DocumentPolicy;
use Src\Company\Document\Domain\Repositories\FolderRepositoryInterface;
use Src\Company\Document\Domain\Repositories\SectionRepositoryInterface;

class DeleteSectionCommand implements CommandInterface
{
    private SectionRepositoryInterface $repository;

    public function __construct(
        private readonly int $section_id
    )
    {
        $this->repository = app()->make(SectionRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('deleteFolder', DocumentPolicy::class);
        return $this->repository->delete($this->section_id);
    }
}