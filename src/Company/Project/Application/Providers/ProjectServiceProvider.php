<?php

namespace Src\Company\Project\Application\Providers;

use Illuminate\Support\ServiceProvider;

class ProjectServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(
            \Src\Company\Project\Domain\Repositories\ProjectRepositoryInterface::class,
            \Src\Company\Project\Application\Repositories\Eloquent\ProjectRepository::class
        );

        $this->app->bind(
            \Src\Company\Project\Domain\Repositories\PropertyRepositoryInterface::class,
            \Src\Company\Project\Application\Repositories\Eloquent\PropertyRepository::class
        );

        $this->app->bind(
            \Src\Company\Project\Domain\Repositories\PropertyMobileRepositoryInterface::class,
            \Src\Company\Project\Application\Repositories\Eloquent\PropertyMobileRepository::class
        );

        $this->app->bind(
            \Src\Company\Project\Domain\Repositories\PropertyTypeRepositoryInterface::class,
            \Src\Company\Project\Application\Repositories\Eloquent\PropertyTypeRepository::class
        );

        $this->app->bind(
            \Src\Company\Project\Domain\Repositories\EventRepositoryInterface::class,
            \Src\Company\Project\Application\Repositories\Eloquent\EventRepository::class
        );

        $this->app->bind(
            \Src\Company\Project\Domain\Repositories\CustomerPaymentRepositoryInterface::class,
            \Src\Company\Project\Application\Repositories\Eloquent\CustomerPaymentRepository::class
        );

        $this->app->bind(
            \Src\Company\Project\Domain\Repositories\SaleReportRepositoryInterface::class,
            \Src\Company\Project\Application\Repositories\Eloquent\SaleReportRepository::class
        );

        $this->app->bind(
            \Src\Company\Project\Domain\Repositories\SaleReportMobileRepositoryInterface::class,
            \Src\Company\Project\Application\Repositories\Eloquent\SaleReportMobileRepository::class
        );

        $this->app->bind(
            \Src\Company\Project\Domain\Repositories\NotificationRepositoryInterface::class,
            \Src\Company\Project\Application\Repositories\Eloquent\NotificationRepository::class
        );

        $this->app->bind(
            \Src\Company\Project\Domain\Repositories\RenovationItemScheduleInterface::class,
            \Src\Company\Project\Application\Repositories\Eloquent\RenovationItemScheduleRepository::class
        );

        $this->app->bind(
            \Src\Company\Project\Domain\Repositories\ProjectReportRepositoryInterface::class,
            \Src\Company\Project\Application\Repositories\Eloquent\ProjectReportRepository::class
        );

        $this->app->bind(
            \Src\Company\Project\Domain\Repositories\ReviewRepositoryInterface::class,
            \Src\Company\Project\Application\Repositories\Eloquent\ReviewRepository::class
        );

        $this->app->bind(
            \Src\Company\Project\Domain\Repositories\SupplierCostingRepositoryInterface::class,
            \Src\Company\Project\Application\Repositories\Eloquent\SupplierCostingRepository::class
        );

        $this->app->bind(
            \Src\Company\Project\Domain\Repositories\SupplierCostingPaymentRepositoryInterface::class,
            \Src\Company\Project\Application\Repositories\Eloquent\SupplierCostingPaymentRepository::class
        );

        $this->app->bind(
            \Src\Company\Project\Domain\Repositories\SupplierCreditRepositoryInterface::class,
            \Src\Company\Project\Application\Repositories\Eloquent\SupplierCreditRepository::class
        );

        $this->app->bind(
            \Src\Company\Project\Domain\Repositories\PaymentTypeRepositoryInterface::class,
            \Src\Company\Project\Application\Repositories\Eloquent\PaymentTypeRepository::class
        );
        
        $this->app->bind(
            \Src\Company\Project\Domain\Repositories\SupplierDebitRepositoryInterface::class,
            \Src\Company\Project\Application\Repositories\Eloquent\SupplierDebitRepository::class
        );

        $this->app->bind(
            \Src\Company\Project\Domain\Repositories\AdvancePaymentRepositoryInterface::class,
            \Src\Company\Project\Application\Repositories\Eloquent\AdvancePaymentRepository::class
        );

        $this->app->bind(
            \Src\Company\Project\Domain\Repositories\VendorInvoiceExpenseTypeRepositoryInterface::class,
            \Src\Company\Project\Application\Repositories\Eloquent\VendorInvoiceExpenseTypeRepository::class
        );

        $this->app->bind(
            \Src\Company\Project\Domain\Repositories\TermAndConditionRepositoryInterface::class,
            \Src\Company\Project\Application\Repositories\Eloquent\TermAndConditionRepository::class
        );

        // Mobile App
        $this->app->bind(
            \Src\Company\Project\Domain\Repositories\ProjectMobileRepositoryInterface::class,
            \Src\Company\Project\Application\Repositories\Eloquent\ProjectMobileRepository::class
        );

        $this->app->bind(
            \Src\Company\Project\Domain\Repositories\PropertyTypeMobileRepositoryInterface::class,
            \Src\Company\Project\Application\Repositories\Eloquent\PropertyTypeMobileRepository::class
        );

        $this->app->bind(
            \Src\Company\Project\Domain\Repositories\RenovationItemScheduleMobileInterface::class,
            \Src\Company\Project\Application\Repositories\Eloquent\RenovationItemScheduleMobileRepository::class
        );

        $this->app->bind(
            \Src\Company\Project\Domain\Repositories\TermAndConditionMobileRepositoryInterface::class,
            \Src\Company\Project\Application\Repositories\Eloquent\TermAndConditionMobileRepository::class
        );

        $this->app->bind(
            \Src\Company\Project\Domain\Repositories\SupplierCostingMobileRepositoryInterface::class,
            \Src\Company\Project\Application\Repositories\Eloquent\SupplierCostingMobileRepository::class
        );

        $this->app->bind(
            \Src\Company\Project\Domain\Repositories\SupplierCostingMobileRepositoryInterface::class,
            \Src\Company\Project\Application\Repositories\Eloquent\SupplierCostingMobileRepository::class
        );

        $this->app->bind(
            \Src\Company\Project\Domain\Repositories\CustomerPaymentMobileRepositoryInterface::class,
            \Src\Company\Project\Application\Repositories\Eloquent\CustomerPaymentMobileRepository::class
        );

        $this->app->bind(
            \Src\Company\Project\Domain\Repositories\SupplierCreditMobileRepositoryInterface::class,
            \Src\Company\Project\Application\Repositories\Eloquent\SupplierCreditMobileRepository::class
        );

    }
}
    