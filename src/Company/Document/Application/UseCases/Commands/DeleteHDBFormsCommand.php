<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Repositories\HDBFormsRepositoryInterface;

class DeleteHDBFormsCommand implements CommandInterface
{
    private HDBFormsRepositoryInterface $repository;

    public function __construct(
        private readonly int $hdb_forms_id
    )
    {
        $this->repository = app()->make(HDBFormsRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->delete($this->hdb_forms_id);
    }
}