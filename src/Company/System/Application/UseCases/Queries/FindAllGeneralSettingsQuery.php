<?php

namespace Src\Company\System\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\System\Domain\Repositories\GeneralSettingRepositoryInterface;

class FindAllGeneralSettingsQuery implements QueryInterface
{
    private GeneralSettingRepositoryInterface $repository;

    public function __construct(
    )
    {
        $this->repository = app()->make(GeneralSettingRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->findAllGeneralSettings();
    }
}