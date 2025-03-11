<?php

namespace Src\Company\Document\Application\Providers;


use Illuminate\Support\ServiceProvider;

class DocumentServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(
            \Src\Company\Document\Domain\Repositories\FolderRepositoryInterface::class,
            \Src\Company\Document\Application\Repositories\Eloquent\FolderRepository::class
        );

        $this->app->bind(
            \Src\Company\Document\Domain\Repositories\DocumentRepositoryInterface::class,
            \Src\Company\Document\Application\Repositories\Eloquent\DocumentRepository::class
        );

        $this->app->bind(
            \Src\Company\Document\Domain\Repositories\ProjectRequirementRepositoryInterface::class,
            \Src\Company\Document\Application\Repositories\Eloquent\ProjectRequirementRepository::class
        );

        $this->app->bind(
            \Src\Company\Document\Domain\Repositories\DocumentStandardRepositoryInterface::class,
            \Src\Company\Document\Application\Repositories\Eloquent\DocumentStandardRepository::class
        );

        $this->app->bind(
            \Src\Company\Document\Domain\Repositories\RenovationDocumentInterface::class,
            \Src\Company\Document\Application\Repositories\Eloquent\RenovationDocumentRepository::class
        );

        $this->app->bind(
            \Src\Company\Document\Domain\Repositories\QuotationTemplateItemsRepositoryInterface::class,
            \Src\Company\Document\Application\Repositories\Eloquent\QuotationTemplateItemsRepository::class
        );

        $this->app->bind(
            \Src\Company\Document\Domain\Repositories\QuotationTemplateItemsRepositoryMobileInterface::class,
            \Src\Company\Document\Application\Repositories\Eloquent\QuotationTemplateItemsMobileRepository::class
        );

        $this->app->bind(
            \Src\Company\Document\Domain\Repositories\DesignWorkRepositoryInterface::class,
            \Src\Company\Document\Application\Repositories\Eloquent\DesignWorkRepository::class
        );

        $this->app->bind(
            \Src\Company\Document\Domain\Repositories\ContractRepositoryInterface::class,
            \Src\Company\Document\Application\Repositories\Eloquent\ContractRepository::class
        );

        $this->app->bind(
            \Src\Company\Document\Domain\Repositories\VendorRepositoryInterface::class,
            \Src\Company\Document\Application\Repositories\Eloquent\VendorRepository::class
        );

        $this->app->bind(
            \Src\Company\Document\Domain\Repositories\MaterialRepositoryInterface::class,
            \Src\Company\Document\Application\Repositories\Eloquent\MaterialRepository::class
        );

        $this->app->bind(
            \Src\Company\Document\Domain\Repositories\HDBFormsRepositoryInterface::class,
            \Src\Company\Document\Application\Repositories\Eloquent\HDBFormsRepository::class
        );

        $this->app->bind(
            \Src\Company\Document\Domain\Repositories\ElectricalPlansRepositoryInterface::class,
            \Src\Company\Document\Application\Repositories\Eloquent\ElectricalPlansRepository::class
        );

        $this->app->bind(
            \Src\Company\Document\Domain\Repositories\EvoRepositoryInterface::class,
            \Src\Company\Document\Application\Repositories\Eloquent\EvoRepository::class
        );

        $this->app->bind(
            \Src\Company\Document\Domain\Repositories\EvoTemplateItemRepositoryInterface::class,
            \Src\Company\Document\Application\Repositories\Eloquent\EvoTemplateItemRepository::class
        );

        $this->app->bind(
            \Src\Company\Document\Domain\Repositories\EvoTemplateRoomRepositoryInterface::class,
            \Src\Company\Document\Application\Repositories\Eloquent\EvoTemplateRoomRepository::class
        );

        $this->app->bind(
            \Src\Company\Document\Domain\Repositories\EvoItemRepositoryInterface::class,
            \Src\Company\Document\Application\Repositories\Eloquent\EvoItemRepository::class
        );

        $this->app->bind(
            \Src\Company\Document\Domain\Repositories\PurchaseOrderRepositoryInterface::class,
            \Src\Company\Document\Application\Repositories\Eloquent\PurchaseOrderRepository::class
        );

        $this->app->bind(
            \Src\Company\Document\Domain\Repositories\PurchaseOrderItemRepositoryInterface::class,
            \Src\Company\Document\Application\Repositories\Eloquent\PurchaseOrderItemRepository::class
        );

        $this->app->bind(
            \Src\Company\Document\Domain\Repositories\HandoverCertificateRepositoryInterface::class,
            \Src\Company\Document\Application\Repositories\Eloquent\HandoverCertificateRepository::class
        );

        $this->app->bind(
            \Src\Company\Document\Domain\Repositories\VariationRepositoryInterface::class,
            \Src\Company\Document\Application\Repositories\Eloquent\VariationRepository::class
        );

        $this->app->bind(
            \Src\Company\Document\Domain\Repositories\CancellationRepositoryInterface::class,
            \Src\Company\Document\Application\Repositories\Eloquent\CancellationRepository::class
        );

        $this->app->bind(
            \Src\Company\Document\Domain\Repositories\FOCRepositoryInterface::class,
            \Src\Company\Document\Application\Repositories\Eloquent\FOCRepository::class
        );

        $this->app->bind(
            \Src\Company\Document\Domain\Repositories\ThreeDDesignRepositoryInterface::class,
            \Src\Company\Document\Application\Repositories\Eloquent\ThreeDDesignRepository::class
        );

        $this->app->bind(
            \Src\Company\Document\Domain\Repositories\TaxInvoiceRepositoryInterface::class,
            \Src\Company\Document\Application\Repositories\Eloquent\TaxInvoiceRepository::class
        );

        $this->app->bind(
            \Src\Company\Document\Domain\Repositories\VendorCategoryRepositoryInterface::class,
            \Src\Company\Document\Application\Repositories\Eloquent\VendorCategoryRepository::class
        );

        $this->app->bind(
            \Src\Company\Document\Domain\Repositories\PurchaseOrderTemplateItemRepositoryInterface::class,
            \Src\Company\Document\Application\Repositories\Eloquent\PurchaseOrderTemplateItemRepository::class
        );

        $this->app->bind(
            \Src\Company\Document\Domain\Repositories\MeasurementRepositoryInterface::class,
            \Src\Company\Document\Application\Repositories\Eloquent\MeasurementRepository::class
        );

        $this->app->bind(
            \Src\Company\Document\Domain\Repositories\PaymentTermRepositoryInterface::class,
            \Src\Company\Document\Application\Repositories\Eloquent\PaymentTermRepository::class
        );


        $this->app->bind(
            \Src\Company\Document\Domain\Repositories\ProjectPortfolioRepositoryInterface::class,
            \Src\Company\Document\Application\Repositories\Eloquent\ProjectPortfolioRepository::class

        );
        
        $this->app->bind(
            \Src\Company\Document\Domain\Repositories\WorkScheduleRepositoryInterface::class,
            \Src\Company\Document\Application\Repositories\Eloquent\WorkSchedulRepository::class
        );

        $this->app->bind(
            \Src\Company\Document\Domain\Repositories\SupplierInvoiceRepositoryInterface::class,
            \Src\Company\Document\Application\Repositories\Eloquent\SupplierInvoiceRepository::class
        );

        $this->app->bind(
            \Src\Company\Document\Domain\Repositories\QuotationTemplateCategoryRepositoryInterface::class,
            \Src\Company\Document\Application\Repositories\Eloquent\QuotationTemplateCategoryRepository::class
        );

        //Mobile App
        $this->app->bind(
            \Src\Company\Document\Domain\Repositories\ProjectRequirementMobileRepositoryInterface::class,
            \Src\Company\Document\Application\Repositories\Eloquent\ProjectRequirementMobileRepository::class
        );

        $this->app->bind(
            \Src\Company\Document\Domain\Repositories\PurchaseOrderMobileRepositoryInterface::class,
            \Src\Company\Document\Application\Repositories\Eloquent\PurchaseOrderMobileRepository::class
        );

        $this->app->bind(
            \Src\Company\Document\Domain\Repositories\PurchaseOrderItemMobileRepositoryInterface::class,
            \Src\Company\Document\Application\Repositories\Eloquent\PurchaseOrderItemMobileRepository::class
        );

        $this->app->bind(
            \Src\Company\Document\Domain\Repositories\RenovationDocumentMobileInterface::class,
            \Src\Company\Document\Application\Repositories\Eloquent\RenovationDocumentMobileRepository::class
        );

        $this->app->bind(
            \Src\Company\Document\Domain\Repositories\EvoMobileRepositoryInterface::class,
            \Src\Company\Document\Application\Repositories\Eloquent\EvoMobileRepository::class
        );

        $this->app->bind(
            \Src\Company\Document\Domain\Repositories\TaxInvoiceMobileRepositoryInterface::class,
            \Src\Company\Document\Application\Repositories\Eloquent\TaxInvoiceMobileRepository::class
        );

        $this->app->bind(
            \Src\Company\Document\Domain\Repositories\HandoverCertificateMobileRepositoryInterface::class,
            \Src\Company\Document\Application\Repositories\Eloquent\HandoverCertificateMobileRepository::class
        );

        $this->app->bind(
            \Src\Company\Document\Domain\Repositories\ContractRepositoryMobileInterface::class,
            \Src\Company\Document\Application\Repositories\Eloquent\ContractMobileRepository::class
        );

        $this->app->bind(
            \Src\Company\Document\Domain\Repositories\VendorMobileRepositoryInterface::class,
            \Src\Company\Document\Application\Repositories\Eloquent\VendorMobileRepository::class
        );

        $this->app->bind(
            \Src\Company\Document\Domain\Repositories\VariationOrderMobileRepositoryInterface::class,
            \Src\Company\Document\Application\Repositories\Eloquent\VariationOrderMobileRepository::class
        );
    }
}
