<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Repositories\MeasurementRepositoryInterface;

class StoreMeasurementCommand implements CommandInterface
{
    private MeasurementRepositoryInterface $repository;

    public function __construct(
        private readonly array $measurements,
    )
    {
        $this->repository = app()->make(MeasurementRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->store($this->measurements);
    }
}