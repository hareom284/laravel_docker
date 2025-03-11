<?php

namespace Src\Company\Document\Domain\Repositories;

use Src\Company\Document\Application\DTO\HDBFormsData;
use Src\Company\Document\Domain\Model\Entities\HDBForms;

interface HDBFormsRepositoryInterface
{
    public function getHDBForms($project_id);

    public function findHDBFormsById(int $id);

    public function store(HDBForms $hDBForms): HDBFormsData;

    public function update(HDBForms $hDBForms): HDBFormsData;

    public function delete(int $hdb_forms_id): void;

}
