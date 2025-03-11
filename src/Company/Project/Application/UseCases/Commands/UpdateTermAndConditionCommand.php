<?php

namespace Src\Company\Project\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Project\Domain\Model\Entities\TermAndCondition;
use Src\Company\Project\Domain\Repositories\TermAndConditionRepositoryInterface;

class UpdateTermAndConditionCommand implements CommandInterface
{
    private TermAndConditionRepositoryInterface $repository;

    public function __construct(
        private readonly TermAndCondition $termAndCondition,
        private readonly array $termAndConditionData
    )
    {
        $this->repository = app()->make(TermAndConditionRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('storeRole', UserPolicy::class);
        return $this->repository->update($this->termAndCondition,$this->termAndConditionData);
    }
}
