<?php

namespace Src\Company\Document\Domain\Repositories;

use Src\Company\Document\Application\DTO\EvoTemplateItemData;
use Src\Company\Document\Domain\Model\Entities\EvoTemplateItems;

interface EvoTemplateItemRepositoryInterface
{
    public function getAllItems();

    public function store(EvoTemplateItems $items): EvoTemplateItemData;

    public function update(EvoTemplateItems $items): EvoTemplateItemData;

    public function delete(int $item_id): void;

}
