<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Project\Domain\Repositories\RenovationItemScheduleMobileInterface;

class StoreRenovationItemScheduleByDocumentIdMobileCommand implements CommandInterface
{
    private RenovationItemScheduleMobileInterface $repository;

    public function __construct(
        private readonly int $documentId
    )
    {
        $this->repository = app()->make(RenovationItemScheduleMobileInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->store($this->documentId);
    }
}