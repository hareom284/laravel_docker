<?php

namespace Src\Company\Project\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Project\Domain\Model\Entities\AdvancePayment;
use Src\Company\Project\Domain\Model\Entities\SupplierDebit;
use Src\Company\Project\Domain\Repositories\AdvancePaymentRepositoryInterface;
use Src\Company\Project\Domain\Repositories\SupplierDebitRepositoryInterface;

class UpdateAdvancePaymentCommand implements CommandInterface
{
    private AdvancePaymentRepositoryInterface $repository;

    public function __construct(
        private readonly AdvancePayment $advancePayment,
    )
    {
        $this->repository = app()->make(AdvancePaymentRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->update($this->advancePayment);
    }
}