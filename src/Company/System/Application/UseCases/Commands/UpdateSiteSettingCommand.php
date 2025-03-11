<?php

namespace Src\Company\System\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\System\Domain\Model\Entities\SiteSetting;
use Src\Company\UserManagement\Domain\Policies\UserPolicy;
use Src\Company\System\Domain\Repositories\SiteSettingRepositoryInterface;

class UpdateSiteSettingCommand implements CommandInterface
{
    private SiteSettingRepositoryInterface $repository;

    public function __construct(
        private readonly SiteSetting $site
    )
    {
        $this->repository = app()->make(SiteSettingRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('updateSiteSetting', UserPolicy::class);
        return $this->repository->update($this->site);
    }
}
