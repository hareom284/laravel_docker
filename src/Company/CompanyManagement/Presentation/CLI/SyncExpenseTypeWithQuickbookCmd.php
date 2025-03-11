<?php

namespace Src\Company\CompanyManagement\Presentation\CLI;

use Illuminate\Console\Command;
use Src\Company\CompanyManagement\Application\UseCases\Commands\SyncExpenseTypeWithQuickbookCommand;
use Src\Company\Document\Application\UseCases\Commands\SyncVendorWithQuickBookCommand;

class SyncExpenseTypeWithQuickbookCmd extends Command
{
	/**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:expense-type';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync expense type data with quickbook and our database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {	
    	(new SyncExpenseTypeWithQuickbookCommand())->execute();
        
        $this->info('Sync expense type data completed successfully!');
    }
}
