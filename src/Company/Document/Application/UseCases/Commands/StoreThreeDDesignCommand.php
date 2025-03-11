<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Model\Entities\ThreeDDesign;
// use Src\Company\Document\Domain\Policies\DocumentPolicy;
use Src\Company\Document\Domain\Repositories\ThreeDDesignRepositoryInterface;

class StoreThreeDDesignCommand implements CommandInterface
{
    private ThreeDDesignRepositoryInterface $repository;

    public function __construct(
        private readonly ThreeDDesign $threeDDesign,
    )
    {
        $this->repository = app()->make(ThreeDDesignRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('storeDesignWork', DocumentPolicy::class);
        return $this->repository->store($this->threeDDesign);
    }
}