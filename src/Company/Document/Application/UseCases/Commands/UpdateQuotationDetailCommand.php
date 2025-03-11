<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Repositories\RenovationDocumentInterface;

class UpdateQuotationDetailCommand implements CommandInterface
{
    private RenovationDocumentInterface $repository;

    public function __construct(
        private readonly ?int $document_id,
        private readonly ?array $data
    )
    {
        $this->repository = app()->make(RenovationDocumentInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('update', DocumentPolicy::class);
        return $this->repository->updateQuotationDetail($this->document_id, $this->data);
    }
}