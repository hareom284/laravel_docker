<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Model\Entities\DocumentStandard;
use Src\Company\Document\Domain\Policies\DocumentPolicy;
use Src\Company\Document\Domain\Repositories\DocumentStandardRepositoryInterface;

class StoreDocumentStandardCommand implements CommandInterface
{
    private DocumentStandardRepositoryInterface $repository;

    public function __construct(
        private readonly DocumentStandard $documentStandard
    )
    {
        $this->repository = app()->make(DocumentStandardRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('store', DocumentPolicy::class);
        return $this->repository->store($this->documentStandard);
    }
}