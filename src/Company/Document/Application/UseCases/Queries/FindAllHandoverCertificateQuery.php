<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Domain\Repositories\HandoverCertificateRepositoryInterface;

class FindAllHandoverCertificateQuery implements QueryInterface
{
    private HandoverCertificateRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = app()->make(HandoverCertificateRepositoryInterface::class);
    }

    public function handle()
    {
        // authorize('findDesignWorkById', DocumentPolicy::class);
        return $this->repository->index();
    }
}