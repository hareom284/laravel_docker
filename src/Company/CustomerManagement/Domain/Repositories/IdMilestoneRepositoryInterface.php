<?php

namespace Src\Company\CustomerManagement\Domain\Repositories;

use Src\Company\CustomerManagement\Application\DTO\IdMilestoneData;
use Src\Company\CustomerManagement\Domain\Model\Entities\IdMilestone;

interface IdMilestoneRepositoryInterface
{

    public function findAllIdMilestones();

    public function findAllIdMilestoneActions();

    public function store(IdMilestone $idMilestone): IdMilestoneData;

    public function update(IdMilestone $idMilestone): IdMilestoneData;

    public function findIdMilestones($id);

    public function updateOrder($idMilestones);

    public function delete(int $idMilestoneId): void;

    public function findIdMilestoneByUserId($id);

    public function getAllWhatsappTemplates();

}
