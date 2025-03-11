<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Repositories\VendorCategoryRepositoryInterface;
class DeleteVendorCategoryCommand implements CommandInterface
{
    private VendorCategoryRepositoryInterface $repository;

    public function __construct(
        private readonly int $vendorCategoryId
    )
    {
        $this->repository = app()->make(VendorCategoryRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->delete($this->vendorCategoryId);
    }
}