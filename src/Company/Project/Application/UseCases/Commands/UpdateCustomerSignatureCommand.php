<?php

namespace Src\Company\Project\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Model\Entities\Contract;
use Src\Company\Project\Domain\Repositories\TermAndConditionRepositoryInterface;

class UpdateCustomerSignatureCommand implements CommandInterface
{
    private TermAndConditionRepositoryInterface $repository;

    public function __construct(
        private readonly ?int $contract_id,
        private readonly ?array $signaturesData,
        private readonly ?array $files,
    )
    {
        $this->repository = app()->make(TermAndConditionRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('storeRole', UserPolicy::class);
        return $this->repository->updateCustomerSignatures($this->contract_id, $this->signaturesData, $this->files);
    }
}
