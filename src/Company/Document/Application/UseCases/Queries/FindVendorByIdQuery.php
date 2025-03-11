<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Application\DTO\SectionData;
use Src\Company\Document\Domain\Policies\DocumentPolicy;
use Src\Company\Document\Domain\Repositories\VendorRepositoryInterface;

class FindVendorByIdQuery implements QueryInterface
{
    private VendorRepositoryInterface $repository;

    public function __construct(
        private readonly int $id,
    )
    {
        $this->repository = app()->make(VendorRepositoryInterface::class);
    }

    public function handle()
    {
        // authorize('findFolderById', DocumentPolicy::class);
        return $this->repository->getVendorById($this->id);
    }
}