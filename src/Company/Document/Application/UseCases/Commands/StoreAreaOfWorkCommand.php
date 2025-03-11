<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Model\Entities\AreaOfWork;
use Src\Company\Document\Domain\Policies\DocumentPolicy;
use Src\Company\Document\Domain\Repositories\AreaOfWorkRepositoryInterface;

class StoreAreaOfWorkCommand implements CommandInterface
{
    private AreaOfWorkRepositoryInterface $repository;

    public function __construct(
        private readonly AreaOfWork $work
    )
    {
        $this->repository = app()->make(AreaOfWorkRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('storeAreaOfWork', DocumentPolicy::class);
        return $this->repository->store($this->work);
    }
}