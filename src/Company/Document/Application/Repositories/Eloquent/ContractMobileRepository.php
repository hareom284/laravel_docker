<?php

namespace Src\Company\Document\Application\Repositories\Eloquent;

use Src\Company\Document\Application\Mappers\ContractMapper;
use Src\Company\Document\Domain\Repositories\ContractRepositoryMobileInterface;
use Src\Company\Document\Domain\Model\Entities\Contract;

class ContractMobileRepository implements ContractRepositoryMobileInterface
{

    public function store(Contract $contract): Contract
    {
        $contractEloquent = ContractMapper::toEloquent($contract);
        
        $contractEloquent->save();

        return ContractMapper::fromEloquent($contractEloquent);

    }

}