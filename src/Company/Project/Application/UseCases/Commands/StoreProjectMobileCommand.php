<?php

namespace Src\Company\Project\Application\UseCases\Commands;

use Illuminate\Http\Request;
use Src\Common\Domain\CommandInterface;
use Src\Company\Project\Domain\Model\Entities\Project;
use Src\Company\Project\Domain\Repositories\ProjectMobileRepositoryInterface;

class StoreProjectMobileCommand implements CommandInterface
{
    private ProjectMobileRepositoryInterface $repository;

    public function __construct(
        private readonly Project $project,
        private readonly Request $request,
    )
    {
        $this->repository = app()->make(ProjectMobileRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->store($this->project, $this->request);
    }
}