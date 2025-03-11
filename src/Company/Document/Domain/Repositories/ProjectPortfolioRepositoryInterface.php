<?php

namespace Src\Company\Document\Domain\Repositories;

use Src\Company\Document\Application\DTO\ProjectPortfolioData;
use Src\Company\Document\Domain\Model\Entities\ProjectPortfolio;

interface ProjectPortfolioRepositoryInterface
{
    public function getProjectPortfolioBySalePerson(int $saleperson_id);

    public function getProjectPortfolioByProjectId(int $projectId);

    public function updateProjectPortfolio($evo_id);

    public function createProjectPortfolio(ProjectPortfolio $evo): ProjectPortfolioData;

    public function deleteProjectPortfolio($request);
}
