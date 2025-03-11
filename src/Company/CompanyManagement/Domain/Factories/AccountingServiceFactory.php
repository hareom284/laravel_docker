<?php
namespace Src\Company\CompanyManagement\Domain\Factories;

use Src\Company\CompanyManagement\Domain\Services\XeroService;
use Src\Company\CompanyManagement\Domain\Services\QuickbookService;

class AccountingServiceFactory
{
    public static function create(string $provider)
    {
        switch (strtolower($provider)) {
            case 'xero':
                return new XeroService();
            case 'quickbooks':
                return new QuickbookService();
            case 'none':
                return null;
            default:
                throw new \InvalidArgumentException("Unsupported accounting provider: {$provider}");
        }
    }
}