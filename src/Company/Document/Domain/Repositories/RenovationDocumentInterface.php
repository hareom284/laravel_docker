<?php

namespace Src\Company\Document\Domain\Repositories;

use Src\Company\Document\Application\DTO\RenovationDocumentData;
use Src\Company\Document\Domain\Model\Entities\RenovationDocuments;

interface RenovationDocumentInterface
{
    public function getRenovationDocumentsIndex($projectId, $type);

    public function getConfirmAmtsByProjectId($projectId);

    public function getRenovationDocuments($renovation_document_id, $type);

    public function getRenovationItemWithSections($projectId);

    public function findTemplateItemsForUpdate($document_id);

    public function store(RenovationDocuments $documents, array $data): RenovationDocumentData;

    public function sendEmailCopy($projectId,$email,$attachment);

    public function changeLeadStatus($renoDocumentId);

    public function updateInvoiceStartNumber($company_id,$invoice_no_start);

    public function getSelectedRenovationDocuments($document_id);

    public function signedQuotationDocument($project_id);

    public function getPendingRenoDoc($filters);

    public function customerSignRenoDocument($data);
    public function deleteQO($document_id);
    
    public function updateQuotationDetail($document_id, $data);
}