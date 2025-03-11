<?php

namespace Src\Common\Presentation\CLI;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Src\Company\Project\Infrastructure\EloquentModels\ProjectEloquentModel;

class UpdateUserStatusCmd extends Command
{
    // Command signature
    protected $signature = 'project:update-user-status';

    // Command description
    protected $description = 'Update user statuses for projects with status "InProgress"';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $inProgressProjects = ProjectEloquentModel::where('project_status', 'InProgress')->with('customersPivot')->get();

        DB::transaction(function () use ($inProgressProjects) {
            foreach ($inProgressProjects as $project) {
                foreach ($project->customersPivot as $pivot) {
                    $pivot->customers->update([
                        'status' => 2
                    ]);
                }
            }
        });

        $this->info('User statuses updated successfully.');
    }
}
