<?php

namespace Src\Company\System\Domain\Repositories;
use Src\Company\System\Domain\Model\Entities\SiteSetting;

interface SiteSettingRepositoryInterface
{
    public function findById($id);

    public function findLogoAndFavicon();

    public function update(SiteSetting $site): SiteSetting;

}
