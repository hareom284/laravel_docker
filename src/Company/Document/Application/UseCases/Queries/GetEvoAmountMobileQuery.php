<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Domain\Repositories\EvoMobileRepositoryInterface;

class GetEvoAmountMobileQuery implements QueryInterface
{
    private EvoMobileRepositoryInterface $repository;

    public function __construct(
        private readonly int $projectId,
    )
    {
        $this->repository = app()->make(EvoMobileRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->getEvoAmt($this->projectId);
    }
}