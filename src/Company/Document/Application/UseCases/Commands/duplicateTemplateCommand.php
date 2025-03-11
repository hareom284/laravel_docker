<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Model\Entities\QuotationTemplateItems;
use Src\Company\Document\Domain\Policies\DocumentPolicy;
use Src\Company\Document\Domain\Repositories\QuotationTemplateItemsRepositoryInterface;

class duplicateTemplateCommand implements CommandInterface
{
    private QuotationTemplateItemsRepositoryInterface $repository;

    public function __construct(
        private readonly mixed $request
    )
    {
        $this->repository = app()->make(QuotationTemplateItemsRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('store', DocumentPolicy::class);
        return $this->repository->duplicateTemplate($this->request);
    }
}