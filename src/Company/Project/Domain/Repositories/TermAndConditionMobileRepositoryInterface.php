<?php

namespace Src\Company\Project\Domain\Repositories;

use Src\Company\Document\Domain\Model\Entities\Contract;

interface TermAndConditionMobileRepositoryInterface
{
    public function storeTermAndConditionSignatures(Contract $contract);
}
