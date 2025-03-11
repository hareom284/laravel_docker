<?php

namespace Src\Company\CompanyManagement\Presentation\CLI;

use Illuminate\Console\Command;
use Src\Company\Document\Application\UseCases\Commands\SyncVendorWithQuickBookCommand;

class SyncVendorWithQuickbookCmd extends Command
{
	/**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:vendor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync vendor data with quickbook and our database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {	
    	(new SyncVendorWithQuickBookCommand())->execute();
        
        $this->info('Sync vendor data completed successfully!');
    }
}
