<?php

namespace Src\Company\Project\Domain\Repositories;

use Src\Company\Document\Domain\Model\Entities\Contract;
use Src\Company\Project\Application\DTO\TermAndConditionData;
use Src\Company\Project\Domain\Model\Entities\TermAndCondition;

interface TermAndConditionRepositoryInterface
{
    public function index($filters = []);
    
    public function getAll();

    public function findTermAndConditionById(int $id);

    public function store(TermAndCondition $termAndCondition,array $termAndConditionData): TermAndConditionData;

    public function update(TermAndCondition $termAndCondition,array $termAndConditionData): TermAndConditionData;

    public function delete(int $termAndConditionId);

    public function storeTermAndConditionSignatures(Contract $contract);

    public function updateCustomerSignatures($contract_id, $signatures, $files);

}
