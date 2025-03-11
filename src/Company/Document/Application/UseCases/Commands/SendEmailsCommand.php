<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Application\DTO\PurchaseOrderData;
use Src\Company\Document\Domain\Repositories\PurchaseOrderRepositoryInterface;

class SendEmailsCommand implements CommandInterface
{
    private PurchaseOrderRepositoryInterface $repository;

    public function __construct(
        private readonly array $emails,
        private readonly PurchaseOrderData $poData
    )
    {
        $this->repository = app()->make(PurchaseOrderRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->sendEmails($this->emails,$this->poData);
    }
}