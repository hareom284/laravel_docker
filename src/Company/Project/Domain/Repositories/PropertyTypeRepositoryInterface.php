<?php

namespace Src\Company\Project\Domain\Repositories;

use Src\Company\Project\Application\DTO\PropertyTypeData;
use Src\Company\Project\Domain\Model\Entities\PropertyType;

interface PropertyTypeRepositoryInterface
{

    public function index();

    public function store($type);

}