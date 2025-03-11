<?php

namespace Src\Company\Project\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Project\Domain\Repositories\PaymentTypeRepositoryInterface;

class FindAllPaymentTypesQuery implements QueryInterface
{
    private PaymentTypeRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = app()->make(PaymentTypeRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->index();
    }
}