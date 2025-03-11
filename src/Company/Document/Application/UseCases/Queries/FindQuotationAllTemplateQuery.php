<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Application\DTO\FolderData;
use Src\Company\Document\Domain\Policies\DocumentPolicy;
use Src\Company\Document\Domain\Repositories\FolderRepositoryInterface;
use Src\Company\Document\Domain\Repositories\QuotationTemplateItemsRepositoryInterface;

class FindQuotationAllTemplateQuery implements QueryInterface
{
    private QuotationTemplateItemsRepositoryInterface $repository;

    public function __construct(
        private readonly mixed $salepersonId,
        private readonly mixed $contract
    )
    {
        $this->repository = app()->make(QuotationTemplateItemsRepositoryInterface::class);
    }

    public function handle()
    {
        // authorize('findFolderById', DocumentPolicy::class);
        return $this->repository->retrieveAllTemplate($this->salepersonId, $this->contract);
    }
}
