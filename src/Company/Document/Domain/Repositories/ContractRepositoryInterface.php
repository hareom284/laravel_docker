<?php

namespace Src\Company\Document\Domain\Repositories;

use Illuminate\Http\Request;
use Src\Company\Document\Application\DTO\ContractData;
use Src\Company\Document\Domain\Model\Entities\Contract;

interface ContractRepositoryInterface
{
    public function getContract($projectId);

    public function getContractAmt($projectId);

    public function store(Contract $contract): Contract;

    public function signContract(Request $request);

    public function customerSign(Request $request);

    public function getContractById($contractId);

}
