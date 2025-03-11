<?php

namespace Src\Company\Document\Domain\Repositories;

interface TaxInvoiceRepositoryInterface
{
    public function signTaxBySale($request);

    public function signTaxByManager($request);

    public function findTaxById($id);
    
    public function getListByStatusOrder(array $filters);

    public function changeStatus($taxId, $status);
}