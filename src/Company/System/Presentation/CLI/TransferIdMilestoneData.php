<?php

namespace Src\Company\System\Presentation\CLI;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Src\Company\CustomerManagement\Infrastructure\EloquentModels\CustomerEloquentModel;
use Src\Company\CustomerManagement\Infrastructure\EloquentModels\IdMilestonesEloquentModel;
use Src\Company\CustomerManagement\Infrastructure\EloquentModels\RejectedReasonsEloquentModel;

class TransferIdMilestoneData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transfer:id-milestone-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Transfter Id Milestone Data from Customers to Id Milestone Table';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $customers = CustomerEloquentModel::all();

        foreach ($customers as $customer) {
            if($customer->id_milestone_id){

                $data = IdMilestonesEloquentModel::query()->where('name',$customer->id_milestone_id)->first();

                if($data)
                {
                    $data->update([
                        'name' => $customer->id_milestone_id
                    ]);
                } else {
                    $data = IdMilestonesEloquentModel::create([
                        'name' => $customer->id_milestone_id,
                        'index' => IdMilestonesEloquentModel::count() + 1
                    ]);
                }

                $customerEloquent = CustomerEloquentModel::query()->where('id',$customer->id)->first();

                $customerEloquent->update([
                    'id_milestone_id' => $data->id
                ]);

                $customerEloquent->idMilestones()->attach($data->id);

            }

            if($customer->rejected_reason_id){
                $data = RejectedReasonsEloquentModel::query()->where('name',$customer->rejected_reason_id)->first();

                if($data)
                {
                    $data->update([
                        'name' => $customer->rejected_reason_id
                    ]);
                } else {
                    $data = RejectedReasonsEloquentModel::create([
                        'name' => $customer->rejected_reason_id,
                        'index' => RejectedReasonsEloquentModel::count() + 1
                    ]);
                }

                $customerEloquent = CustomerEloquentModel::query()->where('id',$customer->id)->first();

                $customerEloquent->update([
                    'rejected_reason_id' => $data->id
                ]);
            }
        }

    }
}
