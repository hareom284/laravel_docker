<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Model\Entities\DesignWork;
use Src\Company\Document\Domain\Policies\DocumentPolicy;
use Src\Company\Document\Domain\Repositories\DesignWorkRepositoryInterface;

class UpdateDesignWorkCommand implements CommandInterface
{
    private DesignWorkRepositoryInterface $repository;

    public function __construct(
        private readonly DesignWork $designWork,
        private readonly array $salepersons_id,
        private readonly array $materials
    )
    {
        $this->repository = app()->make(DesignWorkRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('updateDesignWork', DocumentPolicy::class);
        return $this->repository->update($this->designWork,$this->salepersons_id, $this->materials);
    }
}