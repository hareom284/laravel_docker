<?php

namespace Src\Common\Presentation\CLI;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class CreateLaravelSetupCmd extends Command
{
    protected $signature = 'project:setup'; // The signature for your command

    protected $description = 'initialization Project Setup'; // The description for your command

    public function handle()
    {

        if(app()->environment('production')) {
            if ($this->confirm('!!!Noted!!! You are running in production mode.!Do you wish to continue it may cause production aimeos data loss?')) {
                $this->call('aimeos:setup');

                Schema::table('users', function (Blueprint $table) {
                    $table->dropColumn('name');

                });
                Schema::table('users', function (Blueprint $table) {
                    $table->string('name')->nullable();
                });

            } else {
                $this->info('Aborted!');
            }
        }
        // $this->call('aimeos:setup');

        $this->info('Project setup completed successfully!');
    }
}
