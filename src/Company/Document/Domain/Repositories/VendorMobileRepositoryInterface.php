<?php

namespace Src\Company\Document\Domain\Repositories;

interface VendorMobileRepositoryInterface
{
    public function getVendors($filters = []);

}
