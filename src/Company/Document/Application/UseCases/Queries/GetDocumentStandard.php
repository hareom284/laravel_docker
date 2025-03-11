<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Domain\Repositories\EvoRepositoryInterface;

class GetDocumentStandard implements QueryInterface
{
    private EvoRepositoryInterface $repository;

    public function __construct(
        private readonly int $project_id,
    ) {
        $this->repository = app()->make(EvoRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->getDocumentStandard($this->project_id);
    }
}
