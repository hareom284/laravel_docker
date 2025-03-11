<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Domain\Repositories\VendorRepositoryInterface;

class FindAllVendorQuery implements QueryInterface
{
    private VendorRepositoryInterface $repository;

    public function __construct(
        private readonly array $filters
    )
    {
        $this->repository = app()->make(VendorRepositoryInterface::class);
    }

    public function handle()
    {
        // authorize('findAllDesignWork', DocumentPolicy::class);
        return $this->repository->getVendors($this->filters);
    }
}