<?php

namespace Src\Company\Document\Application\Repositories\Eloquent;

use Illuminate\Support\Facades\DB;
use Src\Company\Document\Domain\Repositories\VendorMobileRepositoryInterface;
use Src\Company\Document\Domain\Resources\VendorResource;
use Src\Company\Document\Infrastructure\EloquentModels\VendorEloquentModel;
use Src\Company\CompanyManagement\Domain\Services\QuickbookService;
use Src\Company\Document\Domain\Resources\VendorMobileResource;

class VendorMobileRepository implements VendorMobileRepositoryInterface
{
    private $quickBookService;

    public function __construct(QuickbookService $quickBookService)
    {
        $this->quickBookService = $quickBookService;
    }

    public function getVendors($filters = [])
    {
        $vendorEloquent = VendorEloquentModel::query()
        ->filter($filters)
        ->orderBy('id', 'desc')->get();

        $finalResults = VendorMobileResource::collection($vendorEloquent);

        return $finalResults;
    }

}
