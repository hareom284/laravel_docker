<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Domain\Repositories\MeasurementRepositoryInterface;

class FindAllMeasurementQuery implements QueryInterface
{
    private MeasurementRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = app()->make(MeasurementRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->getAll();
    }
}