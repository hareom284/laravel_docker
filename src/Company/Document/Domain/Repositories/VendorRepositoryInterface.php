<?php

namespace Src\Company\Document\Domain\Repositories;
use Src\Company\Document\Application\DTO\VendorData;
use Src\Company\Document\Domain\Model\Entities\Vendor;

interface VendorRepositoryInterface
{
    public function getVendors($filters = []);

    public function store(Vendor $vendor): VendorData;

    public function update(Vendor $vendor): VendorData;

    public function delete(int $vendor_id): void;

    public function getVendorById(int $vendor_id);

    public function getVendorByUserId(int $userId);

    public function syncVendorWithQuickBook();

    public function syncWithAccountingSoftwareData($companyId);

}
