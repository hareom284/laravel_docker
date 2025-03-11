<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Model\Entities\Contract;
use Src\Company\Document\Domain\Repositories\ContractRepositoryMobileInterface;

class StoreContractMobileCommand implements CommandInterface
{
    private ContractRepositoryMobileInterface $repository;

    public function __construct(
        private readonly Contract $contract
    )
    {
        $this->repository = app()->make(ContractRepositoryMobileInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->store($this->contract);
    }
}