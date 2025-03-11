<?php

namespace Src\Company\Document\Domain\Repositories;

use Src\Company\Document\Application\DTO\RenovationDocumentData;
use Src\Company\Document\Domain\Model\Entities\RenovationDocuments;

interface RenovationDocumentMobileInterface
{
    public function store(RenovationDocuments $documents, array $data): RenovationDocumentData;

    public function getConfirmAmtsByProjectId($projectId);

    public function findTemplateItemsForUpdate($document_id);

    public function getRenovationDocuments($renovation_document_id, $type);

    public function customerSignRenoDocument($data);

    public function changeLeadStatus($renoDocumentId);

    public function updateInvoiceStartNumber($company_id,$invoice_no_start);
    
}