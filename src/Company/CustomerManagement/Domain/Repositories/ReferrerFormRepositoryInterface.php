<?php

namespace Src\Company\CustomerManagement\Domain\Repositories;

use Src\Company\CustomerManagement\Application\DTO\ReferrerFormData;
use Src\Company\CustomerManagement\Domain\Model\Entities\ReferrerForm;

interface ReferrerFormRepositoryInterface
{

    public function findAllReferrerFormsQuery($filters=[]);

    public function store(ReferrerForm $referrerForm): ReferrerFormData;

    public function sign(ReferrerForm $referrerForm): ReferrerFormData;

    public function findReferrerForm($id);

    public function downloadReferrerForm(int $referrerFormId): void;

    public function findApprovedReferrers();

}
