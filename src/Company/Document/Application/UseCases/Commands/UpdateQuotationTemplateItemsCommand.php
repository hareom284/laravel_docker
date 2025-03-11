<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Model\Entities\QuotationTemplateItems;
use Src\Company\Document\Domain\Policies\DocumentPolicy;
use Src\Company\Document\Domain\Repositories\QuotationTemplateItemsRepositoryInterface;

class UpdateQuotationTemplateItemsCommand implements CommandInterface
{
    private QuotationTemplateItemsRepositoryInterface $repository;

    public function __construct(
        private readonly QuotationTemplateItems $quotationTemplateItems
    )
    {
        $this->repository = app()->make(QuotationTemplateItemsRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('update', DocumentPolicy::class);
        return $this->repository->update($this->quotationTemplateItems);
    }
}