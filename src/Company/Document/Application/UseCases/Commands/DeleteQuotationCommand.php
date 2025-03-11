<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Repositories\RenovationDocumentInterface;

class DeleteQuotationCommand implements CommandInterface
{
    private RenovationDocumentInterface $repository;

    public function __construct(
        private readonly int $document_id
    )
    {
        $this->repository = app()->make(RenovationDocumentInterface::class);
    }

    public function execute(): mixed
    {

        return $this->repository->deleteQO($this->document_id);
    }
}