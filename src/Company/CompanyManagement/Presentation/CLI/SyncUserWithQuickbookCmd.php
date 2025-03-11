<?php

namespace Src\Company\CompanyManagement\Presentation\CLI;

use Illuminate\Console\Command;
use Src\Company\System\Application\UseCases\Commands\SyncUserWithQuickbookCommand;

class SyncUserWithQuickbookCmd extends Command
{
	/**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:customer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync customer data with quickbook and our database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {	
    	(new SyncUserWithQuickbookCommand())->execute();
        
        $this->info('Sync customer data completed successfully!');
    }
}
