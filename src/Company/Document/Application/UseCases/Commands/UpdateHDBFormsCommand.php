<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Model\Entities\HDBForms;
use Src\Company\Document\Domain\Repositories\HDBFormsRepositoryInterface;

class UpdateHDBFormsCommand implements CommandInterface
{
    private HDBFormsRepositoryInterface $repository;

    public function __construct(
        private readonly HDBForms $hdbForms
    )
    {
        $this->repository = app()->make(HDBFormsRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->update($this->hdbForms);
    }
}