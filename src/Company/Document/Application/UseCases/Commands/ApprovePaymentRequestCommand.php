<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Repositories\PaymentTermRepositoryInterface;

class ApprovePaymentRequestCommand implements CommandInterface
{
    private PaymentTermRepositoryInterface $repository;

    public function __construct(
        private ?int $project_id
    )
    {
        $this->repository = app()->make(PaymentTermRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('storeAreaOfWork', DocumentPolicy::class);
        return $this->repository->approvePaymentRequest($this->project_id);
    }
}