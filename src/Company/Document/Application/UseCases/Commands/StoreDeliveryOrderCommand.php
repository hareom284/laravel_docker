<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Model\Entities\DeliveryOrder;
use Src\Company\Document\Domain\Repositories\DeliveryOrderRepositoryInterface;

class StoreDeliveryOrderCommand implements CommandInterface
{
    private DeliveryOrderRepositoryInterface $repository;

    public function __construct(
        private readonly DeliveryOrder $deliveryOrder
    )
    {
        $this->repository = app()->make(DeliveryOrderRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->store($this->deliveryOrder);
    }
}