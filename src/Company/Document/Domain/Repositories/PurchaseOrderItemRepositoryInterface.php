<?php

namespace Src\Company\Document\Domain\Repositories;


interface PurchaseOrderItemRepositoryInterface
{
    public function getAllItems(int $id);
    
	public function store(array $items,$poId): array;

    public function delete(int $item_id): void;
}