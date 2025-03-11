<?php

namespace Src\Company\StaffManagement\Domain\Repositories;

use Src\Company\StaffManagement\Application\DTO\RankData;
use Src\Company\StaffManagement\Domain\Model\Entities\Rank;

interface RankRepositoryInterface
{
    public function getRanks();

    public function store(Rank $rank): RankData;

    public function update(Rank $rank): Rank;

    public function delete(int $rankId): void;

}
