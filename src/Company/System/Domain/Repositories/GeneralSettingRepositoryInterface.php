<?php

namespace Src\Company\System\Domain\Repositories;
use Src\Company\System\Domain\Model\Entities\GeneralSetting;

interface GeneralSettingRepositoryInterface
{

    public function findByName($generalSetting);
    public function update(array $generals): array;

}
