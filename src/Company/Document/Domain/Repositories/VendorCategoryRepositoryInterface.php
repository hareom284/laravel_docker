<?php

namespace Src\Company\Document\Domain\Repositories;

use Src\Company\Document\Application\DTO\VendorCategoryData;
use Src\Company\Document\Domain\Model\Entities\VendorCategory;

interface VendorCategoryRepositoryInterface
{
    public function index();

    public function store(VendorCategory $vendorCategory): VendorCategoryData;

    public function update(VendorCategory $vendorCategory): VendorCategoryData;

    public function delete(int $vendorCategoryId): void;
}
