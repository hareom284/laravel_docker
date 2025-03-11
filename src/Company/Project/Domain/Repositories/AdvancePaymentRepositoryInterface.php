<?php

namespace Src\Company\Project\Domain\Repositories;

use Src\Company\Project\Application\DTO\AdvancePaymentData;
use Src\Company\Project\Domain\Model\Entities\AdvancePayment;
use Src\Company\Project\Infrastructure\EloquentModels\AdvancePaymentEloquentModel;

interface AdvancePaymentRepositoryInterface
{
    public function getBySaleReportId($saleReportId);

    public function getAll(array $filter);

    public function store(AdvancePayment $advancePayment): AdvancePaymentData;

    public function update(AdvancePayment $advancePayment): AdvancePaymentEloquentModel;
}