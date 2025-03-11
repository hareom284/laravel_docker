<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Repositories\VendorRepositoryInterface;


class SyncVendorWithQuickBookCommand implements CommandInterface
{
    private VendorRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = app()->make(VendorRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->syncVendorWithQuickBook();
    }
}