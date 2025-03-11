<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Application\DTO\ThreeDDesignData;
// use Src\Company\Document\Domain\Policies\DocumentPolicy;
use Src\Company\Document\Domain\Repositories\ThreeDDesignRepositoryInterface;

class FindThreeDDesignByIdQuery implements QueryInterface
{
    private ThreeDDesignRepositoryInterface $repository;

    public function __construct(
        private readonly int $id,
    )
    {
        $this->repository = app()->make(ThreeDDesignRepositoryInterface::class);
    }

    public function handle()
    {
        // authorize('findDocumentById', DocumentPolicy::class);
        return $this->repository->getThreeDDesignById($this->id);
    }
}