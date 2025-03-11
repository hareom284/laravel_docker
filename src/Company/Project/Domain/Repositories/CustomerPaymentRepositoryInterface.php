<?php

namespace Src\Company\Project\Domain\Repositories;

use Src\Company\Project\Application\DTO\CustomerPaymentData;
use Src\Company\Project\Domain\Model\Entities\CustomerPayment;

interface CustomerPaymentRepositoryInterface
{
    public function index(array $filters);

    public function getBySaleReportId($saleReportId);

    public function store(CustomerPayment $customerPayment);

    public function importFromQbo(int $projectId);

    public function storeWithQbo(int $companyId);

    public function storeSaleReceiptWithQbo(int $companyId);

    public function update(CustomerPayment $customerPayment);

    public function refundPayment(array $data, int $customer_payment_id);

    public function destroy(int $customer_payment_id): void;

    public function getProjectsForExport();

    public function updateEstimatedDate($customer_payments);
}