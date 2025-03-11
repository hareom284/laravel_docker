<?php

namespace Src\Company\Project\Domain\Repositories;

use Src\Company\Project\Application\DTO\PropertyData;
use Src\Company\Project\Domain\Model\Entities\Property;

interface PropertyMobileRepositoryInterface
{

    public function index();

    public function store(Property $property): PropertyData;

    public function update(Property $property): PropertyData;

}