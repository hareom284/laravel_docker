<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Model\Entities\DesignWork;
use Src\Company\Document\Domain\Policies\DocumentPolicy;
use Src\Company\Document\Domain\Repositories\DesignWorkRepositoryInterface;

class DeleteDesignWorkCommand implements CommandInterface
{
    private DesignWorkRepositoryInterface $repository;

    public function __construct(
        private readonly int $design_work_id
    )
    {
        $this->repository = app()->make(DesignWorkRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('deleteDesignWork', DocumentPolicy::class);
        return $this->repository->delete($this->design_work_id);
    }
}