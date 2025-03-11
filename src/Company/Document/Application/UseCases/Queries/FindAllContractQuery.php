<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Application\DTO\ContractData;
use Src\Company\Document\Domain\Repositories\ContractRepositoryInterface;

class FindAllContractQuery implements QueryInterface
{
    private ContractRepositoryInterface $repository;

    public function __construct(
        private readonly int $projectId
    )
    {
        $this->repository = app()->make(ContractRepositoryInterface::class);
    }

    public function handle()
    {
        // authorize('findAllDesignWork', DocumentPolicy::class);
        return $this->repository->getContract($this->projectId);
    }
}