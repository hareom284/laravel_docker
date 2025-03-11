<?php

namespace Src\Company\System\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\CompanyManagement\Domain\Repositories\AccountingServiceInterface;

class SetupCommand implements CommandInterface
{
    private AccountingServiceInterface $accountingService;

    public function __construct()
    {
        $this->accountingService = app()->make(AccountingServiceInterface::class);
    }

    public function execute(): mixed
    {
        return $this->accountingService->redirectTo();
    }
}