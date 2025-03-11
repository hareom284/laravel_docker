<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Policies\DocumentPolicy;
use Src\Company\Document\Domain\Repositories\VendorRepositoryInterface;

class DeleteVendorCommand implements CommandInterface
{
    private VendorRepositoryInterface $repository;

    public function __construct(
        private readonly int $vendor_id
    )
    {
        $this->repository = app()->make(VendorRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('deleteFolder', DocumentPolicy::class);
        return $this->repository->delete($this->vendor_id);
    }
}