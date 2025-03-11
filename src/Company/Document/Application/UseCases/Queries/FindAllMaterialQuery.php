<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Domain\Repositories\MaterialRepositoryInterface;

class FindAllMaterialQuery implements QueryInterface
{
    private MaterialRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = app()->make(MaterialRepositoryInterface::class);
    }

    public function handle()
    {
        // authorize('findAllDesignWork', DocumentPolicy::class);
        return $this->repository->getMaterials();
    }
}