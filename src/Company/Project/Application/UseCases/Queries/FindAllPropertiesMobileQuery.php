<?php

namespace Src\Company\Project\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Project\Application\DTO\PropertyTypeData;
use Src\Company\Project\Domain\Repositories\PropertyTypeMobileRepositoryInterface;

class FindAllPropertiesMobileQuery implements QueryInterface
{
    private PropertyTypeMobileRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = app()->make(PropertyTypeMobileRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->index();
    }
}