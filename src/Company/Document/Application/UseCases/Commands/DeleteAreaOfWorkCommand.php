<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Model\Entities\AreaOfWork;
use Src\Company\Document\Domain\Policies\DocumentPolicy;
use Src\Company\Document\Domain\Repositories\AreaOfWorkRepositoryInterface;

class DeleteAreaOfWorkCommand implements CommandInterface
{
    private AreaOfWorkRepositoryInterface $repository;

    public function __construct(
        private readonly int $work_id
    )
    {
        $this->repository = app()->make(AreaOfWorkRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('deleteAreaOfWork', DocumentPolicy::class);
        return $this->repository->delete($this->work_id);
    }
}