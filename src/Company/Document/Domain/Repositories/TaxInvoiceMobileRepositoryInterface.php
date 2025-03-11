<?php

namespace Src\Company\Document\Domain\Repositories;

interface TaxInvoiceMobileRepositoryInterface
{
    public function signTaxBySale($request);

    public function findTaxByProjectId($projectId);

    public function findTaxById($id);

}