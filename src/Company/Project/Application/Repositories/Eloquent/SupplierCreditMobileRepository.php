<?php

namespace Src\Company\Project\Application\Repositories\Eloquent;

use Src\Company\Project\Domain\Resources\SupplierCreditResource;
use Src\Company\CompanyManagement\Domain\Services\QuickbookService;
use Src\Company\Project\Infrastructure\EloquentModels\SupplierCreditEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\SupplierCostingEloquentModel;
use Src\Company\Project\Domain\Repositories\SupplierCreditMobileRepositoryInterface;

class SupplierCreditMobileRepository implements SupplierCreditMobileRepositoryInterface
{
    private $quickBookService;

    public function __construct(QuickbookService $quickBookService)
    {
        $this->quickBookService = $quickBookService;
    }

    public function getBySaleReportId($saleReportId)
    {
        $supplierCredits = SupplierCreditEloquentModel::where('sale_report_id', $saleReportId)->get();

        $data['results'] = SupplierCreditResource::collection($supplierCredits);

        $data['sum_amount'] = number_format($supplierCredits->sum('amount'), 2, '.', ',');
        $data['total_amount'] = number_format($supplierCredits->sum('total_amount'), 2, '.', ',');
        $data['total_gst_amount'] = number_format($supplierCredits->sum('gst_amount'), 2, '.', ',');

        return $data;
    }
}
