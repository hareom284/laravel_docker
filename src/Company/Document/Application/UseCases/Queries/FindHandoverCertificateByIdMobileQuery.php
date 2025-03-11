<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Domain\Repositories\HandoverCertificateMobileRepositoryInterface;

class FindHandoverCertificateByIdMobileQuery implements QueryInterface
{
    private HandoverCertificateMobileRepositoryInterface $repository;

    public function __construct(
        private readonly int $id,
    )
    {
        $this->repository = app()->make(HandoverCertificateMobileRepositoryInterface::class);
    }

    public function handle()
    {
        // authorize('findDesignWorkById', DocumentPolicy::class);
        return $this->repository->getHandoverCertificateDetail($this->id);
    }
}