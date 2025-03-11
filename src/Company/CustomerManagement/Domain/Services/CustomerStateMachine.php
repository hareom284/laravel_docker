<?php

namespace Src\Company\CustomerManagement\Domain\Services;

use Illuminate\Support\Facades\DB;
use Src\Company\CustomerManagement\Infrastructure\EloquentModels\CustomerEloquentModel;
use Src\Company\CustomerManagement\Infrastructure\EloquentModels\IdMilestonesEloquentModel;

class CustomerStateMachine
{
    protected $stateMap;
    protected $customer;

    public function __construct($stateMap, CustomerEloquentModel $customer)
    {
        $this->stateMap = $stateMap;
        $this->customer = $customer;
    }

    protected function getCurrentStateId()
    {
        return $this->customer->id_milestone_id;
    }

    protected function getPreviousState()
    {

        $previousHistory = DB::table('status_histories')
            ->where('customer_id', $this->customer->id)
            ->where('id_milestone_id', '!=', $this->customer->id_milestone_id) // Exclude current state
            ->orderBy('created_at', 'desc')
            ->first();

        return $previousHistory ? $previousHistory->id_milestone_id : null;
    }

    public function canTransition(string $action): bool
    {
        $currentStateId = $this->getCurrentStateId();

        if (!isset($this->stateMap['transitions'][$action])) {
            return false;
        }

        foreach ($this->stateMap['transitions'][$action]['transit'] as $transition) {
            if ($transition['from'] == $currentStateId) {
                return true;
            }
        }

        return false;
    }

    public function getNextState(string $action): ?int
    {
        $currentStateId = $this->getCurrentStateId();

        if (!isset($this->stateMap['transitions'][$action])) {
            return null;
        }

        foreach ($this->stateMap['transitions'][$action]['transit'] as $transition) {
            if ($transition['from'] == $currentStateId) {
                return $transition['to'];
            }
        }

        return null;
    }

    public function transition(string $action)
    {
        // if actions is activeLead update hte previous state
        if ($action === 'activeLead') {
            $previousStateId = $this->getPreviousState();

            if ($previousStateId !== null) {
                $this->customer->id_milestone_id = $previousStateId;
                $this->customer->save();

                return $this->customer;
            }

            return null;
        }

        if ($this->canTransition($action)) {
            $nextStateId = $this->getNextState($action);
            
            if ($nextStateId !== null) {
                $this->customer->id_milestone_id = $nextStateId;
                $this->customer->save();
                return $this->customer;
            }
        }

        return null;
    }
}
