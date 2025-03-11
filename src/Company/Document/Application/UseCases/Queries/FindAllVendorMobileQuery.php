<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Domain\Repositories\VendorMobileRepositoryInterface;

class FindAllVendorMobileQuery implements QueryInterface
{
    private VendorMobileRepositoryInterface $repository;

    public function __construct(
        private readonly array $filters
    )
    {
        $this->repository = app()->make(VendorMobileRepositoryInterface::class);
    }

    public function handle()
    {
        // authorize('findAllDesignWork', DocumentPolicy::class);
        return $this->repository->getVendors($this->filters);
    }
}