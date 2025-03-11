<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Application\DTO\DesignWorkData;
use Src\Company\Document\Domain\Policies\DocumentPolicy;
use Src\Company\Document\Domain\Repositories\DesignWorkRepositoryInterface;

class FindDesignWorkByIdQuery implements QueryInterface
{
    private DesignWorkRepositoryInterface $repository;

    public function __construct(
        private readonly int $id,
    )
    {
        $this->repository = app()->make(DesignWorkRepositoryInterface::class);
    }

    public function handle()
    {
        // authorize('findDesignWorkById', DocumentPolicy::class);
        return $this->repository->findDesignWorkById($this->id);
    }
}