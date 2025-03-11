<?php

namespace Src\Common\Infrastructure\Laravel\Kernel;

use Illuminate\Console\Scheduling\Schedule;
use Src\Common\Presentation\CLI\CreateQueryCmd;
use Src\Common\Presentation\CLI\LogoutAllUsers;
use Src\Common\Presentation\CLI\CreateDomainCmd;
use Src\Common\Presentation\CLI\CreateRoutesCmd;
use Src\Common\Presentation\CLI\CreateCommandCmd;
use Src\Common\Presentation\CLI\UpdateTemplatePL;
use Src\Common\Presentation\CLI\CreateControllerCmd;
use Src\Common\Presentation\CLI\CreateTableClearCmd;
use Src\Common\Presentation\CLI\ReformatVendorTable;
use Src\Common\Presentation\CLI\UpdateUserStatusCmd;
use Src\Common\Presentation\CLI\CreateLaravelSetupCmd;
use Src\Common\Presentation\CLI\ReformatDataStructure;
use Src\Common\Presentation\CLI\UpdateMultipleTemplate;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Src\Common\Presentation\CLI\CheckDocumentSectionTotal;
use Src\Common\Presentation\CLI\UpdateCustomerPaymentStatus;
use Src\Common\Presentation\CLI\CompanyPermissionConfiguration;
use Src\Company\System\Presentation\CLI\TransferIdMilestoneData;
use Src\Company\Notification\Application\Jobs\SendNotificationEventJob;
use Src\Company\CompanyManagement\Presentation\CLI\SyncUserWithQuickbookCmd;
use Src\Company\CompanyManagement\Presentation\CLI\SyncVendorWithQuickbookCmd;
use Src\Company\CompanyManagement\Presentation\CLI\SyncBankInfoWithQuickbookCmd;
use Src\Company\System\Infrastructure\EloquentModels\GeneralSettingEloquentModel;
use Src\Company\CompanyManagement\Presentation\CLI\SyncExpenseTypeWithQuickbookCmd;
use Src\Company\CompanyManagement\Application\Jobs\ResetCompanyProjectRunningNumberJob;

class Console extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        CreateDomainCmd::class,
        CreateCommandCmd::class,
        CreateQueryCmd::class,
        CreateControllerCmd::class,
        CreateRoutesCmd::class,
        CreateLaravelSetupCmd::class,
        CreateTableClearCmd::class,
        UpdateMultipleTemplate::class,
        UpdateTemplatePL::class,
        ReformatDataStructure::class,
        SyncUserWithQuickbookCmd::class,
        SyncVendorWithQuickbookCmd::class,
        SyncBankInfoWithQuickbookCmd::class,
        SyncExpenseTypeWithQuickbookCmd::class,
        TransferIdMilestoneData::class,
        LogoutAllUsers::class,
        ReformatVendorTable::class,
        CompanyPermissionConfiguration::class,
        UpdateUserStatusCmd::class,
        UpdateCustomerPaymentStatus::class,
        CheckDocumentSectionTotal::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->job(new SendNotificationEventJob())->everyFiveMinutes();
        // $schedule->job(new SendNotificationEventJob())->everyMinute();
        if(GeneralSettingEloquentModel::where('setting', 'reset_company_quotation_running_number')->exists()){
            $resetOption = GeneralSettingEloquentModel::where('setting', 'reset_company_quotation_running_number')->first();
            if($resetOption->value =='Monthly'){
                $schedule->job(new ResetCompanyProjectRunningNumberJob)->monthlyOn(1, '00:00');
            } else if($resetOption->value == 'Yearly'){
                $schedule->job(new ResetCompanyProjectRunningNumberJob)->yearlyOn(1, 1, '00:00');
            }
        }
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
