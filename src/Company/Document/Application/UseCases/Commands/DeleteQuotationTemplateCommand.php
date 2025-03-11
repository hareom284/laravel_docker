<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Policies\DocumentPolicy;
use Src\Company\Document\Domain\Repositories\QuotationTemplateItemsRepositoryInterface;

class DeleteQuotationTemplateCommand implements CommandInterface
{
    private QuotationTemplateItemsRepositoryInterface $repository;

    public function __construct(
        private readonly int $template_id
    )
    {
        $this->repository = app()->make(QuotationTemplateItemsRepositoryInterface::class);
    }

    public function execute(): mixed
    {

        return $this->repository->deleteTemplate($this->template_id);
    }
}