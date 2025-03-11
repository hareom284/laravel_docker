<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Domain\Repositories\EvoRepositoryInterface;

class FindEvoTemplateQuery implements QueryInterface
{
    private EvoRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = app()->make(EvoRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->index();
    }
}