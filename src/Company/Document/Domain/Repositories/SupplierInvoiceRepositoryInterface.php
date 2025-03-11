<?php

namespace Src\Company\Document\Domain\Repositories;

interface SupplierInvoiceRepositoryInterface
{

    public function getSupplierInvoices(int $projectId);

    public function show($id);

    public function store($projectId, $documentFiles);

    public function delete($id);

}
