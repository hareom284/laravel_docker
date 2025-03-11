<?php

namespace Src\Company\CustomerManagement\Domain\Repositories;

use Src\Company\CustomerManagement\Application\DTO\RejectedReasonData;
use Src\Company\CustomerManagement\Domain\Model\Entities\RejectedReason;

interface RejectedReasonRepositoryInterface
{

    public function findAllRejectedReason();

    public function store(RejectedReason $rejectedReason): RejectedReasonData;

    public function update(RejectedReason $rejectedReason): RejectedReasonData;

    public function updateOrder($rejectedReasons);

    public function findRejectedReason($id);

    public function delete(int $rejectedReasonId): void;

}
