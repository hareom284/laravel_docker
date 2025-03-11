<?php

namespace Src\Company\Project\Application\Repositories\Eloquent;

use Src\Company\Project\Application\DTO\AdvancePaymentData;
use Src\Company\Project\Application\Mappers\AdvancePaymentMapper;
use Src\Company\Project\Domain\Model\Entities\AdvancePayment;
use Src\Company\Project\Domain\Repositories\AdvancePaymentRepositoryInterface;
use Src\Company\Project\Domain\Resources\AdvancePaymentResource;
use Src\Company\Project\Infrastructure\EloquentModels\AdvancePaymentEloquentModel;

class AdvancePaymentRepository implements AdvancePaymentRepositoryInterface
{
    public function getBySaleReportId($saleReportId)
    {
        $data = AdvancePaymentEloquentModel::where('sale_report_id', $saleReportId)->get();

        $total = $data->sum('amount');
        return
        [
            'data' => AdvancePaymentResource::collection($data),
            'total' => number_format($total, 2)
        ];
    }

    public function getAll(array $filter)
    {
        $perPage = $filters['perPage'] ?? 10;
        
        $query = AdvancePaymentEloquentModel::query();

        if (isset($filter['projectId'])) {
            $query->where('sale_report_id', $filter['projectId']);
        }

        if(isset($filter['userId'])) {
            $query->where('user_id', $filter['userId']);
        }

        if(isset($filter['status'])) {
            $query->where('status', $filter['status']);
        }

        $lists = $query->orderBy('status')->orderBy('id','DESC')->paginate($perPage);

        $advancePayments = AdvancePaymentResource::collection($lists);

        $links = [
            'first' => $advancePayments->url(1),
            'last' => $advancePayments->url($advancePayments->lastPage()),
            'prev' => $advancePayments->previousPageUrl(),
            'next' => $advancePayments->nextPageUrl(),
        ];
        $meta = [
            'current_page' => $advancePayments->currentPage(),
            'from' => $advancePayments->firstItem(),
            'last_page' => $advancePayments->lastPage(),
            'path' => $advancePayments->url($advancePayments->currentPage()),
            'per_page' => $perPage,
            'to' => $advancePayments->lastItem(),
            'total' => $advancePayments->total(),
        ];

        return [
            'data' => $advancePayments,
            'links' => $links,
            'meta' => $meta
        ];
    }

    public function store(AdvancePayment $advancePayment): AdvancePaymentData
    {
        $storeAdvancePayment = AdvancePaymentMapper::toEloquent($advancePayment);

        $storeAdvancePayment->save();

        return AdvancePaymentMapper::fromEloquent($storeAdvancePayment);
    }

    public function update(AdvancePayment $advancePayment): AdvancePaymentEloquentModel
    {
        $updateAdvancePayment = AdvancePaymentMapper::toEloquent($advancePayment);

        $updateAdvancePayment->save();

        return $updateAdvancePayment;
    }
}
