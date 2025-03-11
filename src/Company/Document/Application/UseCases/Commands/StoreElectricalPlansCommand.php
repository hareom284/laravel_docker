<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Model\Entities\ElectricalPlans;
use Src\Company\Document\Domain\Policies\DocumentPolicy;
use Src\Company\Document\Domain\Repositories\ElectricalPlansRepositoryInterface;

class StoreElectricalPlansCommand implements CommandInterface
{
    private ElectricalPlansRepositoryInterface $repository;

    public function __construct(
        private readonly ElectricalPlans $electricalPlans,
        private readonly array $salesperson_id,
        private readonly array $materials
    )
    {
        $this->repository = app()->make(ElectricalPlansRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('storeDesignWork', DocumentPolicy::class);
        return $this->repository->store($this->electricalPlans, $this->salesperson_id, $this->materials);
    }
}