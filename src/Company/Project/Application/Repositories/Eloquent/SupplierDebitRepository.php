<?php

namespace Src\Company\Project\Application\Repositories\Eloquent;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Src\Company\CompanyManagement\Domain\Services\QuickbookService;
use Src\Company\Document\Infrastructure\EloquentModels\VendorEloquentModel;
use Src\Company\Project\Application\DTO\SupplierCreditData;
use Src\Company\Project\Application\DTO\SupplierDebitData;
use Src\Company\Project\Application\Mappers\SupplierCreditMapper;
use Src\Company\Project\Application\Mappers\SupplierDebitMapper;
use Src\Company\Project\Domain\Model\Entities\SupplierCredit;
use Src\Company\Project\Domain\Model\Entities\SupplierDebit;
use Src\Company\Project\Domain\Repositories\SupplierDebitRepositoryInterface;
use Src\Company\Project\Domain\Resources\SupplierDebitResource;
use Src\Company\Project\Infrastructure\EloquentModels\SupplierCreditEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\SupplierDebitEloquentModel;

class SupplierDebitRepository implements SupplierDebitRepositoryInterface
{
    public function index(array $filters): array
    {
        $perPage = $filters['perPage'] ?? 10;

        $query = SupplierDebitEloquentModel::query();

        if(isset($filters['vendorId'])){

            $vendorId = $filters['vendorId'];

            $query->where('vendor_id',$vendorId);
        }

        $supplierCreditsEloquent = $query->orderBy('id', 'DESC')->paginate($perPage);

        $supplierCredits =  SupplierDebitResource::collection($supplierCreditsEloquent);

        $links = [
            'first' => $supplierCredits->url(1),
            'last' => $supplierCredits->url($supplierCredits->lastPage()),
            'prev' => $supplierCredits->previousPageUrl(),
            'next' => $supplierCredits->nextPageUrl(),
        ];
        $meta = [
            'current_page' => $supplierCredits->currentPage(),
            'from' => $supplierCredits->firstItem(),
            'last_page' => $supplierCredits->lastPage(),
            'path' => $supplierCredits->url($supplierCredits->currentPage()),
            'per_page' => $perPage,
            'to' => $supplierCredits->lastItem(),
            'total' => $supplierCredits->total(),
        ];

        $responseData['data'] = $supplierCredits;
        $responseData['links'] = $links;
        $responseData['meta'] = $meta;

        return $responseData;
    }

    public function getById($id): SupplierDebitResource
    {
        $supplierDebit = SupplierDebitEloquentModel::query()->findOrFail($id);

        return new SupplierDebitResource($supplierDebit);
    }

    public function getBySaleReportId($saleReportId)
    {
        $supplierDebits = SupplierDebitEloquentModel::where('sale_report_id', $saleReportId)->get();

        $data['results'] = SupplierDebitResource::collection($supplierDebits);

        $data['sum_amount'] = number_format($supplierDebits->sum('amount'), 2, '.', ',');
        $data['total_amount'] = number_format($supplierDebits->sum('total_amount'), 2, '.', ',');
        $data['total_gst_amount'] = number_format($supplierDebits->sum('gst_amount'), 2, '.', ',');

        return $data;
    }

    public function getReport(array $filters)
    {
        $year = $filters['year'];

        $query = SupplierDebitEloquentModel::query()->whereYear('invoice_date', $year);

        if (is_null($filters['vendorId'])) {
            // If month is 0, do not filter by month
            if ($filters['month'] == 0) {

                $result = $query->get();

            } else {
                // Filter by the specified month
                $result = $query->whereMonth('invoice_date', $filters['month'])->get();
            }
        } else {
            // Add vendorId filter to the query
            $query->where('vendor_id', $filters['vendorId']);

            // If month is 0, do not filter by month
            if ($filters['month'] == 0) {
                $result = $query->get();

            } else {
                // Filter by the specified month
                $result = $query->whereMonth('invoice_date', $filters['month'])->get();
            }
        }

        $grandTotal = 0;

        $finalResult = SupplierDebitResource::collection($result)->groupBy(function ($item) {
            return Carbon::parse($item->created_at)->format('M');
        })->map(function ($groupedItems) use (&$grandTotal) {

            $total = $groupedItems->reduce(function ($carry, $item) {
                return $carry + $item->total_amount;
            }, 0);

            $grandTotal += $total;

            return [
                'total' => $total,
                'data' => $groupedItems,
            ];
        });

        return [
            'data' => $finalResult,
            'grand_total' => $grandTotal
        ];
    }

    public function store(SupplierDebit $supplierDebit): SupplierDebitData
    {
        $storeSupplierDebit = SupplierDebitMapper::toEloquent($supplierDebit);

        $storeSupplierDebit->save();

        return SupplierDebitMapper::fromEloquent($storeSupplierDebit);
    }

    
    public function update(SupplierDebit $supplierDebit): SupplierDebitEloquentModel
    {
        $supplierDebitEloquent = SupplierDebitEloquentModel::query()->findOrFail($supplierDebit->id);

        $supplierDebitEloquent->invoice_no = $supplierDebit->invoice_no;

        $supplierDebitEloquent->description = $supplierDebit->description;

        $supplierDebitEloquent->is_gst_inclusive = $supplierDebit->is_gst_inclusive;

        $supplierDebitEloquent->total_amount = $supplierDebit->total_amount;

        $supplierDebitEloquent->amount = $supplierDebit->amount;

        $supplierDebitEloquent->gst_amount = $supplierDebit->gst_amount;

        $supplierDebitEloquent->invoice_date = $supplierDebit->invoice_date;

        $supplierDebitEloquent->vendor_id = $supplierDebit->vendor_id;

        $supplierDebitEloquent->invoice_date = $supplierDebit->invoice_date;

        if($supplierDebit->pdf_path != null)
        {
            $supplierDebitEloquent->pdf_path = $supplierDebit->pdf_path;
        }

        $supplierDebitEloquent->save();

        return $supplierDebitEloquent;
    }
}