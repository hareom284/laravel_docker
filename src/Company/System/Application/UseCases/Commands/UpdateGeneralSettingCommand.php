<?php

namespace Src\Company\System\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\System\Domain\Model\Entities\GeneralSetting;
use Src\Company\UserManagement\Domain\Policies\UserPolicy;
use Src\Company\System\Domain\Repositories\GeneralSettingRepositoryInterface;

class UpdateGeneralSettingCommand implements CommandInterface
{
    private GeneralSettingRepositoryInterface $repository;

    public function __construct(
        private readonly array $general
    )
    {
        $this->repository = app()->make(GeneralSettingRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('updateSiteSetting', UserPolicy::class);
        return $this->repository->update($this->general);
    }
}
