<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Application\DTO\EvoTemplateItemData;
use Src\Company\Document\Domain\Repositories\EvoTemplateItemRepositoryInterface;

class FindAllEvoTemplateItemQuery implements QueryInterface
{
    private EvoTemplateItemRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = app()->make(EvoTemplateItemRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->getAllItems();
    }
}