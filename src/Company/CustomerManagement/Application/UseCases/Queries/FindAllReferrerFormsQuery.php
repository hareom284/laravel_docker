<?php

namespace Src\Company\CustomerManagement\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\CustomerManagement\Domain\Repositories\ReferrerFormRepositoryInterface;

class FindAllReferrerFormsQuery implements QueryInterface
{

    private ReferrerFormRepositoryInterface $repository;
    public function __construct(
        private readonly array $filters
    )
    {
        $this->repository = app()->make(ReferrerFormRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->findAllReferrerFormsQuery($this->filters);
    }
}
