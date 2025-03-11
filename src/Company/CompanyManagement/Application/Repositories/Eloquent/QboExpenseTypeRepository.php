<?php

namespace Src\Company\CompanyManagement\Application\Repositories\Eloquent;

use Src\Company\CompanyManagement\Domain\Repositories\AccountingServiceInterface;
use Src\Company\CompanyManagement\Domain\Repositories\QboExpenseTypeRepositoryInterface;
use Src\Company\CompanyManagement\Infrastructure\EloquentModels\QboExpenseTypeEloquentModel;
use Src\Company\System\Infrastructure\EloquentModels\GeneralSettingEloquentModel;

class QboExpenseTypeRepository implements QboExpenseTypeRepositoryInterface
{
    private $accountingService;

    public function __construct(
        AccountingServiceInterface $accountingService = null
    )
    {
        $this->accountingService = $accountingService;
    }

    public function syncWithAccountingSoftwareData($companyId)
    {
        $generalSettingEloquent = GeneralSettingEloquentModel::where('setting', 'accounting_software_integration')->first();

        $expenseTypes = $this->accountingService->getAllExpenseAccount($companyId);
        
        if($generalSettingEloquent->value == 'quickbooks'){

            foreach ($expenseTypes as $expense) {
                
                QboExpenseTypeEloquentModel::updateOrCreate(
                    ['quick_book_id' => $expense->Id, 'company_id' => $companyId],
                    ['name' => $expense->Name, ]
                );
            }

        }else{

            foreach ($expenseTypes as $expense) {

                QboExpenseTypeEloquentModel::updateOrCreate(
                    ['xero_id' => $expense->getAccountId()],
                    ['name' => $expense->getName(), 'company_id' => $companyId]
                );
            }
        }

        return true;
    }

    public function getAllExpenseTypes()
    {
        $expenseTypes = QboExpenseTypeEloquentModel::select('id','name','quick_book_id','company_id')->get();

        return $expenseTypes;
    }

}