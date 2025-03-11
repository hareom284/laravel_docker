<?php

namespace Src\Company\Document\Domain\Repositories;

use Src\Company\Document\Domain\Model\Entities\Contract;

interface ContractRepositoryMobileInterface
{
    public function store(Contract $contract): Contract;
}
