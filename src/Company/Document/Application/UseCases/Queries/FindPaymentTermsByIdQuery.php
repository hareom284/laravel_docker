<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Domain\Repositories\PaymentTermRepositoryInterface;

class FindPaymentTermsByIdQuery implements QueryInterface
{
    private PaymentTermRepositoryInterface $repository;

    public function __construct(
        private readonly int $id,
    )
    {
        $this->repository = app()->make(PaymentTermRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->findPaymentTermById($this->id);
    }
}