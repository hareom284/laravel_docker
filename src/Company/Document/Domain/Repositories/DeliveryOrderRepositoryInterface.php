<?php

namespace Src\Company\Document\Domain\Repositories;

use Src\Company\Document\Domain\Model\Entities\DeliveryOrder;

interface DeliveryOrderRepositoryInterface
{
    public function findDeliveryOrderByProjectId(int $projectId);

    public function findDeliveryOrderById(int $id);

    public function store(DeliveryOrder $deliveryOrder);

}
