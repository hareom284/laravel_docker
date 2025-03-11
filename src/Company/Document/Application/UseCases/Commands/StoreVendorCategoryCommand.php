<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Model\Entities\VendorCategory;
use Src\Company\Document\Domain\Repositories\VendorCategoryRepositoryInterface;

class StoreVendorCategoryCommand implements CommandInterface
{
    private VendorCategoryRepositoryInterface $repository;

    public function __construct(
        private readonly VendorCategory $vendorCategory
    )
    {
        $this->repository = app()->make(VendorCategoryRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('storeFolder', DocumentPolicy::class);
        return $this->repository->store($this->vendorCategory);
    }
}