<?php

namespace Src\Company\Project\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Project\Domain\Repositories\PaymentTypeRepositoryInterface;

class FindPaymentTypesByIdQuery implements QueryInterface
{
    private PaymentTypeRepositoryInterface $repository;

    public function __construct(
        private readonly int $id,
    )
    {
        $this->repository = app()->make(PaymentTypeRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->findPaymentTypeById($this->id);
    }
}