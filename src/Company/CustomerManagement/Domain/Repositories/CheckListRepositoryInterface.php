<?php

namespace Src\Company\CustomerManagement\Domain\Repositories;

use Src\Company\CustomerManagement\Domain\Model\Entities\CheckList;

interface CheckListRepositoryInterface
{
    public function store(CheckList $checkList);

    public function delete(int $checklist_id);

    public function completeCheckList(CheckList $checkList,int $checklist_id);

    public function checkListByCustomerId(int $user_id);

}
