<?php

namespace Src\Company\Project\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Project\Domain\Repositories\TermAndConditionRepositoryInterface;

class FindAllTermAndConditionQuery implements QueryInterface
{
    private TermAndConditionRepositoryInterface $repository;

    public function __construct(
        private readonly array $filters,
    )
    {
        $this->repository = app()->make(TermAndConditionRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->index($this->filters);
    }
}
