<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Repositories\QuotationTemplateCategoryRepositoryInterface;

class MoveQuotationTemplateCategoryCommand implements CommandInterface
{
    private QuotationTemplateCategoryRepositoryInterface $repository;

    public function __construct(
        private readonly int $template_id,
        private readonly array $data
    )
    {
        $this->repository = app()->make(QuotationTemplateCategoryRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->moveTemplate($this->data, $this->template_id);
    }
}