<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Domain\Repositories\HandoverCertificateRepositoryInterface;

class FindHandoverByProjectIdQuery implements QueryInterface
{
    private HandoverCertificateRepositoryInterface $repository;

    public function __construct(
        private readonly int $projectId,
    )
    {
        $this->repository = app()->make(HandoverCertificateRepositoryInterface::class);
    }

    public function handle()
    {
        // authorize('findDesignWorkById', DocumentPolicy::class);
        return $this->repository->getHandoverByProjectId($this->projectId);
    }
}