<?php

namespace Src\Company\Project\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
// use Src\Company\Project\Domain\Policies\ProjectPolicy;
use Src\Company\Project\Domain\Repositories\ProjectRepositoryInterface;
use Src\Company\Project\Domain\Repositories\PropertyRepositoryInterface;

class DeletePropertyCommand implements CommandInterface
{
    private PropertyRepositoryInterface $repository;

    public function __construct(
        private readonly int $property_id
    )
    {
        $this->repository = app()->make(PropertyRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('deleteProperty', ProjectPolicy::class);
        return $this->repository->destroy($this->property_id);
    }
}