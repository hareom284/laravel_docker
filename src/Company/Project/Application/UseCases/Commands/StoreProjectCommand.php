<?php

namespace Src\Company\Project\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Model\Document;
use Src\Company\Project\Domain\Model\Entities\Project;
use Src\Company\Project\Domain\Model\Entities\Property;
// use Src\Company\Project\Domain\Policies\ProjectPolicy;
use Src\Company\Project\Domain\Repositories\ProjectRepositoryInterface;

class StoreProjectCommand implements CommandInterface
{
    private ProjectRepositoryInterface $repository;

    public function __construct(
        private readonly Project $project,
        private readonly int $property_id,
        private readonly array $salespersonIds,
        private readonly ?Document $document = null,
        private readonly string $block_num
    )
    {
        $this->repository = app()->make(ProjectRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('store', ProjectPolicy::class);
        return $this->repository->store($this->project, $this->property_id, $this->salespersonIds, $this->document, $this->block_num);
    }
}