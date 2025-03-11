<?php

namespace Src\Company\Project\Application\Repositories\Eloquent;

use Src\Company\Project\Domain\Resources\SupplierCostingResource;
use Src\Company\Project\Infrastructure\EloquentModels\SupplierCostingEloquentModel;
use Src\Company\CompanyManagement\Domain\Services\QuickbookService;
use Src\Company\Project\Domain\Repositories\SupplierCostingMobileRepositoryInterface;

class SupplierCostingMobileRepository implements SupplierCostingMobileRepositoryInterface
{
    private $quickBookService;

    public function __construct(QuickbookService $quickBookService)
    {
        $this->quickBookService = $quickBookService;
    }

    public function index(array $filters)
    {
        $perPage = $filters['perPage'] ?? 10;

        $query = SupplierCostingEloquentModel::where('project_id', '!=', 0);

        if(isset($filters['vendorId'])){

            $vendorId = $filters['vendorId'];

            $query->where('vendor_id',$vendorId);
        }

        if(isset($filters['projectId'])){

            $projectId = $filters['projectId'];

            $query->where('project_id',$projectId);
        }

        if (isset($filters['designerId'])) {

            $designerId = $filters['designerId'];

            // Filter the SupplierCostingEloquentModel based on designerId
            $query->whereHas('project', function ($query) use ($designerId) {
                $query->whereHas('salespersons', function ($query) use ($designerId) {
                    $query->where('salesperson_id', $designerId);
                });
            });
        }

        if(isset($filters['status'])){

            $status = $filters['status'];

            $query->where('status',$status);
        }

        $supplierCostingsEloquent = $query->orderBy('status','ASC')->orderBy('invoice_date','DESC')->paginate($perPage);

        $supplierCostings =  SupplierCostingResource::collection($supplierCostingsEloquent);

        $links = [
            'first' => $supplierCostings->url(1),
            'last' => $supplierCostings->url($supplierCostings->lastPage()),
            'prev' => $supplierCostings->previousPageUrl(),
            'next' => $supplierCostings->nextPageUrl(),
        ];
        $meta = [
            'current_page' => $supplierCostings->currentPage(),
            'from' => $supplierCostings->firstItem(),
            'last_page' => $supplierCostings->lastPage(),
            'path' => $supplierCostings->url($supplierCostings->currentPage()),
            'per_page' => $perPage,
            'to' => $supplierCostings->lastItem(),
            'total' => $supplierCostings->total(),
        ];

        $responseData['data'] = $supplierCostings;
        $responseData['links'] = $links;
        $responseData['meta'] = $meta;

        return $responseData;
    }


    public function getByProjectId($projectId)
    {
        $qboConfig = config('quickbooks');

        $supplierCostings = SupplierCostingEloquentModel::where('project_id', $projectId)
            ->where(function ($query) {
                // Retrieve where there is a PO relationship and PO status is 3
                $query->whereHas('purchaseOrder', function ($query) {
                    $query->where('status', 3);
                });

                // OR retrieve where there is no PO relationship
                $query->orWhereDoesntHave('purchaseOrder');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $data['data'] = SupplierCostingResource::collection($supplierCostings);
        $data['is_qbo_integration'] = $qboConfig['qbo_integration'];

        return $data;
    }
}
