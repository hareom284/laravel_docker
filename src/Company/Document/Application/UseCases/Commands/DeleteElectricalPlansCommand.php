<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Policies\DocumentPolicy;
use Src\Company\Document\Domain\Repositories\ElectricalPlansRepositoryInterface;

class DeleteElectricalPlansCommand implements CommandInterface
{
    private ElectricalPlansRepositoryInterface $repository;

    public function __construct(
        private readonly int $electrical_plans_id
    )
    {
        $this->repository = app()->make(ElectricalPlansRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('deleteDesignWork', DocumentPolicy::class);
        return $this->repository->delete($this->electrical_plans_id);
    }
}