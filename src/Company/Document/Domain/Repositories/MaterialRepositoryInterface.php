<?php

namespace Src\Company\Document\Domain\Repositories;
use Src\Company\Document\Domain\Model\Entities\Folder;
use Src\Company\Document\Application\DTO\FolderData;

interface MaterialRepositoryInterface
{
    public function getMaterials();

}
