<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Illuminate\Http\UploadedFile;
use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Repositories\SupplierInvoiceRepositoryInterface;

class StoreSupplierInvoiceCommand implements CommandInterface
{
    private SupplierInvoiceRepositoryInterface $repository;

    public function __construct(
        private readonly int $projectId,
        private readonly array $documentFiles,
    )
    {
        $this->repository = app()->make(SupplierInvoiceRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->store($this->projectId, $this->documentFiles);
    }
}