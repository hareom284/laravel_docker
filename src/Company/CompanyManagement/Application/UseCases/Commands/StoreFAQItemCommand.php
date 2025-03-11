<?php

namespace Src\Company\CompanyManagement\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\CompanyManagement\Domain\Model\Entities\FAQItem;
use Src\Company\CompanyManagement\Domain\Repositories\FAQItemRepositoryInterface;

class StoreFAQItemCommand implements CommandInterface
{
    private FAQItemRepositoryInterface $repository;

    public function __construct(
        private readonly FAQItem $faq
    )
    {
        $this->repository = app()->make(FAQItemRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->store($this->faq);
    }
}