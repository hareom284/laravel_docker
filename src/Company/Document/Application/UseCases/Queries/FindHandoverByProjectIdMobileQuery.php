<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Domain\Repositories\HandoverCertificateMobileRepositoryInterface;

class FindHandoverByProjectIdMobileQuery implements QueryInterface
{
    private HandoverCertificateMobileRepositoryInterface $repository;

    public function __construct(
        private readonly int $projectId,
    )
    {
        $this->repository = app()->make(HandoverCertificateMobileRepositoryInterface::class);
    }

    public function handle()
    {
        // authorize('findDesignWorkById', DocumentPolicy::class);
        return $this->repository->getHandoverByProjectId($this->projectId);
    }
}