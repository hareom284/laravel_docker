<?php

namespace Src\Company\Document\Application\Repositories\Eloquent;

use Src\Company\Document\Application\DTO\DeliveryOrderData;
use Src\Company\Document\Domain\Model\Entities\DeliveryOrder;
use Src\Company\Document\Domain\Repositories\DeliveryOrderRepositoryInterface;
use Src\Company\Document\Domain\Resources\DeliveryOrderResource;
use Src\Company\Document\Infrastructure\EloquentModels\DeliveryOrderEloquentModel;

class DeliveryOrderRepository implements DeliveryOrderRepositoryInterface
{

    public function findDeliveryOrderByProjectId(int $projectId)
    {

        $deliveryOrderEloquent = DeliveryOrderEloquentModel::query()->where('project_id',$projectId)->first();

        $deliveryOrder = new DeliveryOrderResource($deliveryOrderEloquent);

        return $deliveryOrder;
    }

    public function findDeliveryOrderById(int $id)
    {
        
        $deliveryOrderEloquent = DeliveryOrderEloquentModel::query()->with('materials','designer.user','assistantDesigner.user','project.properties','project.salespersons','project.customers')->findOrFail($id);

        $deliveryOrderData = new DeliveryOrderResource($deliveryOrderEloquent);

        return $deliveryOrderData;
    }

    public function store(DeliveryOrder $deliveryOrder)
    {


    }
}
