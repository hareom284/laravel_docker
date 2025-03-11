<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Model\Entities\Contract;
use Src\Company\Document\Domain\Repositories\ContractRepositoryInterface;

class SignCustomerContractCommand implements CommandInterface
{
    private ContractRepositoryInterface $repository;

    public function __construct(
        private $request
    )
    {
        $this->repository = app()->make(ContractRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('storeAreaOfWork', DocumentPolicy::class);
        return $this->repository->customerSign($this->request);
    }
}