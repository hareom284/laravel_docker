<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Model\Entities\Section;
use Src\Company\Document\Domain\Policies\DocumentPolicy;
use Src\Company\Document\Domain\Repositories\SectionRepositoryInterface;

class UpdateSectionCommand implements CommandInterface
{
    private SectionRepositoryInterface $repository;

    public function __construct(
        private readonly Section $section
    )
    {
        $this->repository = app()->make(SectionRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('updateFolder', DocumentPolicy::class);
        return $this->repository->update($this->section);
    }
}