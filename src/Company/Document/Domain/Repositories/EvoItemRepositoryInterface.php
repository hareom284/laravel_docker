<?php

namespace Src\Company\Document\Domain\Repositories;

use Src\Company\Document\Application\DTO\EvoItemData;

interface EvoItemRepositoryInterface
{
    public function store(array $items,$evoId): array;

    public function delete(int $item_id): void;

}
