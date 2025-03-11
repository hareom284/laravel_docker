<?php

namespace Src\Company\System\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\System\Domain\Repositories\GeneralSettingRepositoryInterface;

class FindGeneralSettingByNameQuery implements QueryInterface
{
    private GeneralSettingRepositoryInterface $repository;

    public function __construct(
        private readonly string $generalSetting,
    )
    {
        $this->repository = app()->make(GeneralSettingRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->findByName($this->generalSetting);
    }
}