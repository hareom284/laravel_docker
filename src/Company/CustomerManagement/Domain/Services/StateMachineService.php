<?php
namespace Src\Company\CustomerManagement\Domain\Services;

use Src\Company\CustomerManagement\Infrastructure\EloquentModels\CustomerEloquentModel;

class StateMachineService
{

    public function transition($customer_id)
    {
        $customer = CustomerEloquentModel::find($customer_id);
        $currentIdMilestone = $customer->currentIdMilestone;
        if($currentIdMilestone){
            $nextIdMilestone = $currentIdMilestone->toIdMilestones()->first();
            if ($nextIdMilestone) {
                $customer->update([
                    'id_milestone_id' => $nextIdMilestone->id
                ]);
            }
        }
    }
}
