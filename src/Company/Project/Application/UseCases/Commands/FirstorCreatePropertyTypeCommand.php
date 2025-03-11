<?php

namespace Src\Company\Project\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Project\Domain\Repositories\PropertyTypeRepositoryInterface;

class FirstorCreatePropertyTypeCommand implements CommandInterface
{
    private PropertyTypeRepositoryInterface $repository;

    public function __construct(
        private readonly string $type
    )
    {
        $this->repository = app()->make(PropertyTypeRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->store($this->type);
    }
}