<?php

namespace Src\Common\Presentation\CLI;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Src\Company\Project\Infrastructure\EloquentModels\CustomerPaymentEloquentModel;
use Src\Company\System\Infrastructure\EloquentModels\GeneralSettingEloquentModel;

class UpdateCustomerPaymentStatus extends Command
{
    // The name and signature of the console command.
    protected $signature = 'payment:update-status';

    // The console command description.
    protected $description = 'Update customer payment status based on conditions';

    // Execute the console command.
    public function handle()
    {
        // Check if QuickBooks integration is enabled
        $enableQuickBookIntegration = false;
        $generalSettingEloquent = GeneralSettingEloquentModel::where('setting', 'accounting_software_integration')->first();

        if($generalSettingEloquent && $generalSettingEloquent->value != 'none'){
            $enableQuickBookIntegration = true;
        }

        if (!$enableQuickBookIntegration) {
            // Update status for customer payments based on condition
            $payments = CustomerPaymentEloquentModel::all();

            foreach ($payments as $payment) {
                if (!empty($payment->paid_invoice_file_path)) {
                    // Set status to 1 if the paid_invoice_file_path is not null or empty
                    $payment->status = 1;
                } else {
                    // Set status to 0 if the paid_invoice_file_path is null or empty
                    $payment->status = 0;
                }

                $payment->save();
            }

            $this->info('Customer payment statuses have been updated.');
        } else {
            $this->info('QuickBooks integration is enabled. No updates were made.');
        }
    }
}