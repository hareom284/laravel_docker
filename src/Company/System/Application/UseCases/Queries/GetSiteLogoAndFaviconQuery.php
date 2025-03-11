<?php

namespace Src\Company\System\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\System\Domain\Repositories\SiteSettingRepositoryInterface;

class GetSiteLogoAndFaviconQuery implements QueryInterface
{
    private SiteSettingRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = app()->make(SiteSettingRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->findLogoAndFavicon();
    }
}