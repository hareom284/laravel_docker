<?php
namespace Src\Company\CompanyManagement\Application\Providers;

use Illuminate\Support\ServiceProvider;
use Src\Company\CompanyManagement\Domain\Factories\AccountingServiceFactory;
use Src\Company\CompanyManagement\Domain\Repositories\AccountingServiceInterface;
use Src\Company\System\Infrastructure\EloquentModels\GeneralSettingEloquentModel;

class AccountingServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(AccountingServiceInterface::class, function ($app) {
            $setting = GeneralSettingEloquentModel::where('setting', 'accounting_software_integration')->first();
            
            if(isset($setting))
                return AccountingServiceFactory::create($setting->value);

            return null;
        });
    }

    public function boot()
    {
        //
    }
}
