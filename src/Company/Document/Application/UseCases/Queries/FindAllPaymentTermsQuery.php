<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Domain\Repositories\PaymentTermRepositoryInterface;

class FindAllPaymentTermsQuery implements QueryInterface
{
    private PaymentTermRepositoryInterface $repository;

    public function __construct(
        private readonly array $filters
    )
    {
        $this->repository = app()->make(PaymentTermRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->index($this->filters);
    }
}