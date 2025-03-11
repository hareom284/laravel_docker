<?php

namespace Src\Company\CompanyManagement\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\CompanyManagement\Domain\Repositories\BankInfoRepositoryInterface;
use Src\Company\CompanyManagement\Domain\Repositories\QboExpenseTypeRepositoryInterface;
use Src\Company\Document\Domain\Repositories\VendorRepositoryInterface;
use Src\Company\Project\Domain\Repositories\CustomerPaymentRepositoryInterface;
use Src\Company\Project\Domain\Repositories\SupplierCostingRepositoryInterface;
use Src\Company\UserManagement\Application\Repositories\Eloquent\UserRepository;
use Src\Company\UserManagement\Domain\Repositories\UserRepositoryInterface;

class SyncDataWithAccountingSoftwareCommand implements CommandInterface
{
    private BankInfoRepositoryInterface $bankInfoRepoInterface;
    private QboExpenseTypeRepositoryInterface $expenseTypeRepoInterface;
    private UserRepositoryInterface $userRepoInterface;
    private VendorRepositoryInterface $vendorRepoInterface;
    private CustomerPaymentRepositoryInterface $customerPaymentRepoInterface;
    private SupplierCostingRepositoryInterface $supplierCostingRepoInterface;

    public function __construct(
        private readonly int $entity,
        private readonly int $companyId
    )
    {
        $this->bankInfoRepoInterface = app()->make(BankInfoRepositoryInterface::class);
        $this->expenseTypeRepoInterface = app()->make(QboExpenseTypeRepositoryInterface::class);
        $this->userRepoInterface = app()->make(UserRepositoryInterface::class);
        $this->vendorRepoInterface = app()->make(VendorRepositoryInterface::class);
        $this->customerPaymentRepoInterface = app()->make(CustomerPaymentRepositoryInterface::class);
        $this->supplierCostingRepoInterface = app()->make(SupplierCostingRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        switch ($this->entity) {
            case '1':
                return $this->userRepoInterface->syncWithAccountingSoftwareData($this->companyId);
            case '2':
                return $this->vendorRepoInterface->syncWithAccountingSoftwareData($this->companyId);
            case '3':
                return $this->expenseTypeRepoInterface->syncWithAccountingSoftwareData($this->companyId);
            case '4':
                return $this->bankInfoRepoInterface->syncWithAccountingSoftwareData($this->companyId);
            case '5':
                return $this->customerPaymentRepoInterface->storeWithQbo($this->companyId);
            case '6':
                return $this->supplierCostingRepoInterface->storeWithQbo($this->companyId);
            case '7':
                return $this->customerPaymentRepoInterface->storeSaleReceiptWithQbo($this->companyId);
        }
    }
}