<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Policies\DocumentPolicy;
use Src\Company\Document\Domain\Repositories\QuotationTemplateItemsRepositoryInterface;

class DeleteQuotationTemplateItemsCommand implements CommandInterface
{
    private QuotationTemplateItemsRepositoryInterface $repository;

    public function __construct(
        private readonly int $items_id
    )
    {
        $this->repository = app()->make(QuotationTemplateItemsRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('delete', DocumentPolicy::class);
        return $this->repository->delete($this->items_id);
    }
}