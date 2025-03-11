<?php

namespace Src\Company\Project\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Project\Application\DTO\PropertyTypeData;
use Src\Company\Project\Domain\Repositories\PropertyTypeRepositoryInterface;

class FindAllPropertiesQuery implements QueryInterface
{
    private PropertyTypeRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = app()->make(PropertyTypeRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->index();
    }
}