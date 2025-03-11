<?php

namespace Src\Company\StaffManagement\Domain\Repositories;

use Src\Company\StaffManagement\Domain\Model\Staff;

interface StaffRepositoryInterface
{
    public function store(Staff $staff);

    public function update(Staff $staff);

}
