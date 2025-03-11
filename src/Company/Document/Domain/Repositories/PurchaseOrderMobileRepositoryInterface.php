<?php

namespace Src\Company\Document\Domain\Repositories;

use Src\Company\Document\Application\DTO\PurchaseOrderData;
use Src\Company\Document\Domain\Model\Entities\PurchaseOrder;

interface PurchaseOrderMobileRepositoryInterface
{

	public function getAllPurchaseOrders($filters = []);

	public function getPurchaseOrderByProjectId($projectId);

	public function store(PurchaseOrder $po): PurchaseOrderData;

	public function updatePo($data,$id);
    
	public function findById(int $id);

	public function getPurchaseOrderNumberCount();

}