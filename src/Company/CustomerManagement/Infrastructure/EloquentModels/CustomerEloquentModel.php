<?php

declare(strict_types=1);

namespace Src\Company\CustomerManagement\Infrastructure\EloquentModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Src\Company\Project\Infrastructure\EloquentModels\CustomerPaymentEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\PropertyEloquentModel;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Src\Company\CustomerManagement\Domain\Services\CustomerStateMachine;
use Src\Company\System\Infrastructure\EloquentModels\CampaignAudiencesEloquentModel;
use Src\Company\CustomerManagement\Infrastructure\EloquentModels\CheckListEloquentModel;
use Src\Company\CustomerManagement\Infrastructure\EloquentModels\CheckListTemplateItemEloquentModel;
use Src\Company\CustomerManagement\Infrastructure\EloquentModels\IdMilestonesEloquentModel;
use Src\Company\CustomerManagement\Infrastructure\EloquentModels\RejectedReasonsEloquentModel;
use Src\Company\StaffManagement\Infrastructure\EloquentModels\StaffEloquentModel;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;

class CustomerEloquentModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'customers';

    public $timestamps = false;

    protected $fillable = [
        'nric',
        'attachment',
        'status',
        'source',
        'additional_information',
        'assigned_by_management_id',
        'user_id',
        'last_modified_by',
        'inactive_at',
        'inactive_reason',
        'company_name',
        'customer_type',
        'budget',
        'quote_value',
        'book_value',
        'key_collection',
        'id_milestone_id',
        'rejected_reason_id',
        'next_meeting',
        'days_aging',
        'remarks',
        'budget_value'
    ];

    public function user()
    {
        return $this->belongsTo(UserEloquentModel::class, 'user_id', 'id');
    }

    public function modified_by()
    {
        return $this->belongsTo(UserEloquentModel::class, 'last_modified_by', 'id');
    }

    public function staffs()
    {
        return $this->belongsToMany(StaffEloquentModel::class, 'salespersons_customers', 'customer_uid', 'salesperson_uid');
    }

    public function assign_staff()
    {
        return $this->belongsTo(UserEloquentModel::class, 'assigned_by_management_id', 'id');
    }

    public function check_lists()
    {
        return $this->hasMany(CheckListEloquentModel::class, 'customer_id', 'id');
    }

    public function customer_payments()
    {
        return $this->hasMany(CustomerPaymentEloquentModel::class, 'customer_id');
    }

    // list of Customer Milestones
    public function idMilestones()
    {
        return $this->belongsToMany(IdMilestonesEloquentModel::class, 'status_histories','customer_id','id_milestone_id')->withPivot('remark','message_sent','duration','file')->withTimestamps();
    }

    // customer current Milestone
    public function currentIdMilestone()
    {
        return $this->belongsTo(IdMilestonesEloquentModel::class,'id_milestone_id','id');
    }

    public function rejectedReason()
    {
        return $this->belongsTo(RejectedReasonsEloquentModel::class,'rejected_reason_id','id');
    }

    /**
     * The roles that belong to the CustomerEloquentModel
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function customer_properties(): BelongsToMany
    {
        return $this->belongsToMany(PropertyEloquentModel::class, 'customer_properties', 'customer_id', 'property_id');
    }

    public function leadCheckLists()
    {
        return $this->belongsToMany(CheckListTemplateItemEloquentModel::class, 'lead_checklist_items', 'customer_id', 'checklist_template_item_id')->withPivot('status', 'date_completed')->withTimestamps();
    }

    public function campaignAudience()
    {
        return $this->hasOne(CampaignAudiencesEloquentModel::class,'customer_id');
    }

    // public function transition(string $action)
    // {
    //     $stateMachine = new CustomerStateMachine($this);
    //     $stateMachine->transition($action);
    //     return $this;
    // }
    public function getStateMap()
    {
        if($this->id_milestone_id){
            $milestone = IdMilestonesEloquentModel::with('fromTransitions.fromMilestone', 'fromTransitions.toMilestone')
            ->find($this->id_milestone_id);
             // Initialize the state map structure
             $stateMap = [
                "states" => [],
                "transitions" => [],
                "initial" => null,
                "final" => []
            ];

            // Add the initial state
            $stateMap["states"][] = $milestone->id;
            $stateMap["initial"] = $milestone->id;

            // Iterate through the transitions
            foreach ($milestone->fromTransitions as $transition) {
                // Add from and to states to the states array
                if (!in_array($transition->fromMilestone->id, $stateMap["states"])) {
                    $stateMap["states"][] = $transition->fromMilestone->id;
                }
                if (!in_array($transition->toMilestone->id, $stateMap["states"])) {
                    $stateMap["states"][] = $transition->toMilestone->id;
                }

                // Add the transition to the transitions array
                if (!isset($stateMap["transitions"][$transition->action])) {
                    $stateMap["transitions"][$transition->action] = [
                        "transit" => []
                    ];
                }

                // Add the from-to transition
                $stateMap["transitions"][$transition->action]["transit"][] = [
                    "from" => $transition->fromMilestone->id,
                    "to" => $transition->toMilestone->id
                ];

                // Identify final states (states without any further transitions)
                if (empty($transition->toMilestone->action)) {
                    $stateMap["final"][$transition->to_id_milestone_id] = $transition->toMilestone->id;
                }
            }
            return $stateMap;
        }else{
            return null;
        }
    }
    public function transition(string $action)
    {
        $stateMap = $this->getStateMap();
        $stateMachine = new CustomerStateMachine($stateMap, $this);
        return $stateMachine->transition($action);
    }

}
