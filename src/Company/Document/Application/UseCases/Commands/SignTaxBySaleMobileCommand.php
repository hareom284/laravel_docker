<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Repositories\TaxInvoiceMobileRepositoryInterface;

class SignTaxBySaleMobileCommand implements CommandInterface
{
    private TaxInvoiceMobileRepositoryInterface $repository;

    public function __construct(
        private $request
    )
    {
        $this->repository = app()->make(TaxInvoiceMobileRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('storeAreaOfWork', DocumentPolicy::class);
        return $this->repository->signTaxBySale($this->request);
    }
}