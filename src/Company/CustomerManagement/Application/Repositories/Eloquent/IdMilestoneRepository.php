<?php

namespace Src\Company\CustomerManagement\Application\Repositories\Eloquent;

use Illuminate\Support\Facades\Config;
use Src\Company\CustomerManagement\Application\DTO\IdMilestoneData;
use Src\Company\CustomerManagement\Application\Mappers\IdMilestoneMapper;
use Src\Company\CustomerManagement\Domain\Model\Entities\IdMilestone;
use Src\Company\CustomerManagement\Domain\Repositories\IdMilestoneRepositoryInterface;
use Src\Company\CustomerManagement\Domain\Resources\IdMilestoneResources;
use Src\Company\CustomerManagement\Infrastructure\EloquentModels\CustomerEloquentModel;
use Src\Company\CustomerManagement\Infrastructure\EloquentModels\IdMilestonesEloquentModel;
use Src\Company\CustomerManagement\Infrastructure\EloquentModels\IdMilestoneTransitionEloquentModel;

class IdMilestoneRepository implements IdMilestoneRepositoryInterface
{

    public function findAllIdMilestones()
    {
        $idMilestoneEloquent = IdMilestonesEloquentModel::query()->orderBy('index', 'asc')->get();

        $idMilestones = IdMilestoneResources::collection($idMilestoneEloquent);

        return $idMilestones;
    }

    public function findAllIdMilestoneActions()
    {
        $idMilestoneActions = config('id-milestone-actions.actions');

        return $idMilestoneActions;
    }

    public function store(IdMilestone $idMilestone): IdMilestoneData
    {
        $idMilestoneEloquent = IdMilestoneMapper::toEloquent($idMilestone);

        $idMilestoneEloquent->save();

        $transitions = json_decode($idMilestone->transitions, true);
        if ($transitions) {
            foreach ($transitions as $transition) {
                foreach ($transition['actions'] as $action) {
                    IdMilestoneTransitionEloquentModel::create([
                        'from_id_milestone_id' => $idMilestoneEloquent->id,
                        'action' => $action,
                        'to_id_milestone_id' => $transition['to'],
                    ]);
                }
            }
        }

        return IdMilestoneData::fromEloquent($idMilestoneEloquent);
    }

    public function findIdMilestones($id)
    {
        $idMilestone = IdMilestonesEloquentModel::with('fromTransitions')->find($id);
        return new IdMilestoneResources($idMilestone);
    }

    public function update(IdMilestone $idMilestone): IdMilestoneData
    {
        $idMilestoneEloquent = IdMilestoneMapper::toEloquent($idMilestone);

        $idMilestoneEloquent->save();

        $transitions = json_decode($idMilestone->transitions, true);
        if ($transitions) {
            // Delete old transitions
            $idMilestoneEloquent->fromTransitions()->delete();

            // Insert new transitions
            foreach ($transitions as $transition) {
                foreach ($transition['actions'] as $action) {
                    IdMilestoneTransitionEloquentModel::create([
                        'from_id_milestone_id' => $idMilestoneEloquent->id,
                        'action' => $action,
                        'to_id_milestone_id' => $transition['to'],
                    ]);
                }
            }
        }

        return IdMilestoneData::fromEloquent($idMilestoneEloquent);
    }

    public function updateOrder($idMilestones)
    {
        $decoded_milestones = json_decode($idMilestones);
        if ($decoded_milestones) {
            foreach ($decoded_milestones as $idMilestone) {
                $idMilestoneEloquent = IdMilestonesEloquentModel::query()->findOrFail($idMilestone->id);

                $idMilestoneEloquent->index = $idMilestone->index;

                $idMilestoneEloquent->update();
            }
        }

        return $idMilestones;
    }

    public function delete(int $idMilestoneId): void
    {
        $idMilestoneEloquent = IdMilestonesEloquentModel::query()->findOrFail($idMilestoneId);

        $idMilestoneEloquent->delete();
    }

    public function findIdMilestoneByUserId($id)
    {
        $customer = CustomerEloquentModel::where('user_id', $id)->first();
        if ($customer) {
            $idMilestone = IdMilestonesEloquentModel::find($customer->id_milestone_id);
            return $idMilestone;
        }
        return null;
    }

    public function getAllWhatsappTemplates()
    {
        $templates = Config::get('whatsapp_templates.templates');
        return $templates;
    }
}
