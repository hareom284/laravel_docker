<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Repositories\EvoRepositoryInterface;

class SignEVOCommand implements CommandInterface
{
    private EvoRepositoryInterface $repository;

    public function __construct(
        private $request
    )
    {
        $this->repository = app()->make(EvoRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('storeAreaOfWork', DocumentPolicy::class);
        return $this->repository->customerSign($this->request);
    }
}