<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Repositories\TaxInvoiceRepositoryInterface;

class SignTaxByManagerCommand implements CommandInterface
{
    private TaxInvoiceRepositoryInterface $repository;

    public function __construct(
        private $request
    )
    {
        $this->repository = app()->make(TaxInvoiceRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('storeAreaOfWork', DocumentPolicy::class);
        return $this->repository->signTaxByManager($this->request);
    }
}