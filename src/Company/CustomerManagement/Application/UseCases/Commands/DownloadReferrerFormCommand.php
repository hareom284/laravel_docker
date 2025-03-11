<?php

namespace Src\Company\CustomerManagement\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\CustomerManagement\Domain\Repositories\ReferrerFormRepositoryInterface;

class DownloadReferrerFormCommand implements CommandInterface
{
    private ReferrerFormRepositoryInterface $repository;

    public function __construct(
        private readonly int $id
    )
    {
        $this->repository = app()->make(ReferrerFormRepositoryInterface::class);
    }

    public function execute()
    {
        return $this->repository->downloadReferrerForm($this->id);
    }
}
