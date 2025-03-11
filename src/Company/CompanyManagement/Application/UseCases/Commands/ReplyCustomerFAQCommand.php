<?php

namespace Src\Company\CompanyManagement\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\CompanyManagement\Domain\Model\Entities\FAQItem;
use Src\Company\CompanyManagement\Domain\Repositories\FAQItemRepositoryInterface;

class ReplyCustomerFAQCommand implements CommandInterface
{
    private FAQItemRepositoryInterface $repository;

    public function __construct(
        private readonly int $id,
        private readonly ?string $answer = null
    )
    {
        $this->repository = app()->make(FAQItemRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->replyCustomerFAQ($this->id,$this->answer);
    }
}