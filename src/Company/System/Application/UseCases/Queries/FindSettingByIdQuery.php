<?php

namespace Src\Company\System\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\System\Domain\Repositories\SiteSettingRepositoryInterface;

class FindSettingByIdQuery implements QueryInterface
{
    private SiteSettingRepositoryInterface $repository;

    public function __construct(
        private readonly int $id,
    )
    {
        $this->repository = app()->make(SiteSettingRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->findById($this->id);
    }
}