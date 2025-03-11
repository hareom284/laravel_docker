<?php

namespace Src\Company\Project\Domain\Repositories;

use Src\Company\Project\Application\DTO\PropertyData;
use Src\Company\Project\Domain\Model\Entities\Property;

interface PropertyRepositoryInterface
{

    public function index();

    public function store(Property $property): PropertyData;

    public function update(Property $property): PropertyData;

    public function destroy(int $property_id): void;

}