<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Model\Entities\Vendor;
use Src\Company\Document\Domain\Policies\DocumentPolicy;
use Src\Company\Document\Domain\Repositories\VendorRepositoryInterface;

class UpdateVendorCommand implements CommandInterface
{
    private VendorRepositoryInterface $repository;

    public function __construct(
        private readonly Vendor $vendor
    )
    {
        $this->repository = app()->make(VendorRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('updateFolder', DocumentPolicy::class);
        return $this->repository->update($this->vendor);
    }
}