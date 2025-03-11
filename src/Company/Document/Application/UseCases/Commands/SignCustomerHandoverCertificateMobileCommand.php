<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Repositories\HandoverCertificateMobileRepositoryInterface;

class SignCustomerHandoverCertificateMobileCommand implements CommandInterface
{
    private HandoverCertificateMobileRepositoryInterface $repository;

    public function __construct(
        private $request
    )
    {
        $this->repository = app()->make(HandoverCertificateMobileRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->customerSign($this->request);
    }
}