<?php

namespace Src\Company\Document\Domain\Repositories;

use Src\Company\Document\Application\DTO\PurchaseOrderTemplateItemData;
use Src\Company\Document\Domain\Model\Entities\PurchaseOrderTemplateItem;

interface PurchaseOrderTemplateItemRepositoryInterface
{
	public function index($filters = []);

	public function getItemsForPoCreate($companyId,$vendorCategoryId);

	public function store(PurchaseOrderTemplateItem $poTemplateItem): PurchaseOrderTemplateItemData;

	public function update(PurchaseOrderTemplateItem $poTemplateItem): PurchaseOrderTemplateItemData;
	
	public function delete(int $po_id): void;
}