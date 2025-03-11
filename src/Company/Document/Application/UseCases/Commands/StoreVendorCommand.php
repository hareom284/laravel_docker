<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Model\Entities\Vendor;
use Src\Company\Document\Domain\Repositories\VendorRepositoryInterface;

class StoreVendorCommand implements CommandInterface
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
        // authorize('storeFolder', DocumentPolicy::class);
        return $this->repository->store($this->vendor);
    }
}