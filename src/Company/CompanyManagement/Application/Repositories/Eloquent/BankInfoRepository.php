<?php

namespace Src\Company\CompanyManagement\Application\Repositories\Eloquent;

use Illuminate\Support\Facades\Log;
use Src\Company\CompanyManagement\Domain\Repositories\AccountingServiceInterface;
use Src\Company\CompanyManagement\Domain\Repositories\BankInfoRepositoryInterface;
use Src\Company\CompanyManagement\Domain\Services\QuickbookService;
use Src\Company\CompanyManagement\Infrastructure\EloquentModels\BankInfoEloquentModel;
use Src\Company\System\Infrastructure\EloquentModels\GeneralSettingEloquentModel;

class BankInfoRepository implements BankInfoRepositoryInterface
{
    private $accountingService;

    public function __construct(AccountingServiceInterface $accountingService = null)
    {
        $this->accountingService = $accountingService;
    }

    public function getAllBankInfos()
    {
        $bankInfos = BankInfoEloquentModel::get();

        return $bankInfos;
    }

    public function syncWithAccountingSoftwareData(int $companyId)
    {
        $generalSettingEloquent = GeneralSettingEloquentModel::where('setting', 'accounting_software_integration')->first();

        $bankInfos = $this->accountingService->getAllAccount($companyId);

        if($generalSettingEloquent->value == 'quickbooks'){
            
            foreach ($bankInfos as $bankInfo) {
            
                BankInfoEloquentModel::create([
                    'bank_name' => $bankInfo->Name,
                    'quick_book_account_id' => $bankInfo->Id,
                    'company_id' => $companyId

                ]);
            }

        }else{

            foreach ($bankInfos as $bankInfo) {

                BankInfoEloquentModel::create([
                    'bank_name' => $bankInfo->getName(),
                    'xero_account_id' => $bankInfo->getAccountId(),
                    'company_id' => $companyId
                ]);
            }
        }
        
        return $bankInfos;
    }

}