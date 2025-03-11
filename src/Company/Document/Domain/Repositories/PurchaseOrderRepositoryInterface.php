<?php

namespace Src\Company\Document\Domain\Repositories;

use Src\Company\Document\Application\DTO\PurchaseOrderData;
use Src\Company\Document\Domain\Model\Entities\PurchaseOrder;

interface PurchaseOrderRepositoryInterface
{
	public function getAllPurchaseOrders($filters = []);

	public function getByProjectId($projectId);

	public function getPurchaseOrderByProjectId($projectId);

	public function getPurchaseOrderByStatus($status);

	public function getPurchaseOrderNumberCount();

	public function findById(int $id);

	public function findManagerEmails();

	public function sendEmails($emails,PurchaseOrderData $poData);

	public function store(PurchaseOrder $po): PurchaseOrderData;

	public function updatePo($data,$id);

	public function update($purchaseOrder,$id);

	public function managerSign($request);
	
	public function delete(int $po_id): void;

	public function findQuotationItemsForPOQuery($projectId);
}