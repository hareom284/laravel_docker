<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Repositories\PaymentTermRepositoryInterface;

class SendPaymentRequestCommand implements CommandInterface
{
    private PaymentTermRepositoryInterface $repository;

    public function __construct(
        private ?int $project_id,
        private $request
    )
    {
        $this->repository = app()->make(PaymentTermRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('storeAreaOfWork', DocumentPolicy::class);
        return $this->repository->sendRequestNote($this->project_id, $this->request);
    }
}