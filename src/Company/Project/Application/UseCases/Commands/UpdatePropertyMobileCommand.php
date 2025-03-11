<?php

namespace Src\Company\Project\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Project\Domain\Model\Entities\Property;
use Src\Company\Project\Domain\Policies\ProjectPolicy;
use Src\Company\Project\Domain\Repositories\PropertyMobileRepositoryInterface;

class UpdatePropertyMobileCommand implements CommandInterface
{
    private PropertyMobileRepositoryInterface $repository;

    public function __construct(
        private readonly Property $property
    )
    {
        $this->repository = app()->make(PropertyMobileRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('updateProperty', ProjectPolicy::class);
        return $this->repository->update($this->property);
    }
}