<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Domain\Repositories\DeliveryOrderRepositoryInterface;

class FindDeliveryOrderByIdQuery implements QueryInterface
{
    private DeliveryOrderRepositoryInterface $repository;

    public function __construct(
        private readonly int $id,
    )
    {
        $this->repository = app()->make(DeliveryOrderRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->findDeliveryOrderById($this->id);
    }
}