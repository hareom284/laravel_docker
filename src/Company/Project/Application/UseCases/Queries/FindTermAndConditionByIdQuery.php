<?php

namespace Src\Company\Project\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Project\Domain\Repositories\TermAndConditionRepositoryInterface;

class FindTermAndConditionByIdQuery implements QueryInterface
{
    private TermAndConditionRepositoryInterface $repository;

    public function __construct(
        private readonly int $id,
    )
    {
        $this->repository = app()->make(TermAndConditionRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->findTermAndConditionById($this->id);
    }
}
