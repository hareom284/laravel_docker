<?php

namespace Src\Company\Project\Application\Repositories\Eloquent;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Src\Company\Project\Domain\Model\Entities\CustomerPayment;
use Src\Company\Project\Domain\Resources\CustomerPaymentResource;
use Src\Company\Project\Application\Mappers\CustomerPaymentMapper;
use Src\Company\CompanyManagement\Domain\Services\QuickbookService;
use Src\Company\Project\Domain\Resources\AllCustomerPaymentResource;
use Src\Company\Project\Infrastructure\EloquentModels\SaleReportEloquentModel;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;
use Src\Company\System\Infrastructure\EloquentModels\GeneralSettingEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\CustomerPaymentEloquentModel;
use Src\Company\Project\Domain\Repositories\CustomerPaymentMobileRepositoryInterface;

class CustomerPaymentMobileRepository implements CustomerPaymentMobileRepositoryInterface
{

    private $quickBookService;

    public function __construct(QuickbookService $quickBookService)
    {
        $this->quickBookService = $quickBookService;
    }

    public function getBySaleReportId($saleReportId)
    {
        $customerPayments = CustomerPaymentEloquentModel::query()
            ->where('sale_report_id', $saleReportId)
            ->get();

        $sortedPayments = $customerPayments
            ->sortBy('index')
            ->sortBy('payment_type')
            ->values();

        $finalResults = CustomerPaymentResource::collection($sortedPayments);

        return $finalResults;
    }
}
