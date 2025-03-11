<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Domain\Repositories\EvoRepositoryInterface;

class FindEVOByIdQuery implements QueryInterface
{
    private EvoRepositoryInterface $repository;

    public function __construct(
        private readonly int $evo_id,
    )
    {
        $this->repository = app()->make(EvoRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->findEvoById($this->evo_id);
    }
}