<?php

namespace Src\Company\CustomerManagement\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\CustomerManagement\Domain\Model\Entities\ReferrerForm;
use Src\Company\CustomerManagement\Domain\Repositories\ReferrerFormRepositoryInterface;

class StoreReferrerFormCommand implements CommandInterface
{
    private ReferrerFormRepositoryInterface $repository;

    public function __construct(
        private readonly ReferrerForm $referrerForm
    )
    {
        $this->repository = app()->make(ReferrerFormRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->store($this->referrerForm);
    }
}
