<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Domain\Repositories\VendorCategoryRepositoryInterface;

class FindAllVendorCategoryQuery implements QueryInterface
{
    private VendorCategoryRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = app()->make(VendorCategoryRepositoryInterface::class);
    }

    public function handle()
    {
        // authorize('findAllDesignWork', DocumentPolicy::class);
        return $this->repository->index();
    }
}