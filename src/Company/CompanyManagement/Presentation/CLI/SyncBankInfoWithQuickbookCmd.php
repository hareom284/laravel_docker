<?php

namespace Src\Company\CompanyManagement\Presentation\CLI;

use Illuminate\Console\Command;
use Src\Company\CompanyManagement\Application\UseCases\Commands\SyncBankInfoWithQuickbookCommand;

class SyncBankInfoWithQuickbookCmd extends Command
{
	/**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:bank-info';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync bank info data with quickbook and our database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {	
    	(new SyncBankInfoWithQuickbookCommand())->execute();
        
        $this->info('Sync bank info data completed successfully!');
    }
}
