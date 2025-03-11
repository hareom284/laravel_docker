<?php

namespace Src\Company\Project\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Project\Domain\Repositories\PropertyTypeMobileRepositoryInterface;

class FirstorCreatePropertyTypeMobileCommand implements CommandInterface
{
    private PropertyTypeMobileRepositoryInterface $repository;

    public function __construct(
        private readonly string $type
    )
    {
        $this->repository = app()->make(PropertyTypeMobileRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->store($this->type);
    }
}