<?php

namespace Src\Company\CustomerManagement\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\CustomerManagement\Domain\Repositories\ReferrerFormRepositoryInterface;

class FindApprovedReferrers implements QueryInterface
{

    private ReferrerFormRepositoryInterface $repository;
    public function __construct()
    {
        $this->repository = app()->make(ReferrerFormRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->findApprovedReferrers();
    }
}
