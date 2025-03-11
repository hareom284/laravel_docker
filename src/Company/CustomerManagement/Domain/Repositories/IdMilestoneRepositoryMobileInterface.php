<?php

namespace Src\Company\CustomerManagement\Domain\Repositories;

use Src\Company\CustomerManagement\Application\DTO\IdMilestoneData;
use Src\Company\CustomerManagement\Domain\Model\Entities\IdMilestone;

interface IdMilestoneRepositoryMobileInterface
{

    public function findAllIdMilestones();

}
