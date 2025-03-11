<?php

namespace Src\Company\Document\Application\Repositories\Eloquent;

use Src\Company\Document\Application\DTO\ProjectPortfolioData;
use Src\Company\Document\Domain\Model\Entities\ProjectPortfolio;
use Src\Company\Document\Domain\Repositories\ProjectPortfolioRepositoryInterface;
use Src\Company\Document\Domain\Resources\ProjectPorfolioEloquentModelResource;
use Src\Company\Document\Infrastructure\EloquentModels\ProjectPorfolioEloquentModel;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;

class ProjectPortfolioRepository implements ProjectPortfolioRepositoryInterface
{
    /******
     * these will used
     * show all the project portfolio on the saleperson lists
     * on mobile
     ****/
    public function getProjectPortfolioBySalePerson(int $saleperson_id)
    {



        $user = UserEloquentModel::where('id', $saleperson_id)
            ->with(['projects'])
            ->first();

        // Check if the user exists and has projects
        $project_ids = $user?->projects->pluck('id')->toArray() ?? [];




        $projectPortfolio = ProjectPorfolioEloquentModel::whereIn('project_id',$project_ids)->with('project','media')->get();




        return ProjectPorfolioEloquentModelResource::collection($projectPortfolio);


    }

    /***
     * these will used show the project porfilo by project id
     * on the project project profolio on web
     *
     */
    public function getProjectPortfolioByProjectId(int $projectId)
    {
        $projectPortfolio = ProjectPorfolioEloquentModel::where('project_id', $projectId)->with('media')->get();

        return ProjectPorfolioEloquentModelResource::collection($projectPortfolio);
    }

    public function updateProjectPortfolio($evo_id) {}

    public function createProjectPortfolio(ProjectPortfolio $evo): ProjectPortfolioData {}

    public function deleteProjectPortfolio($request) {}
}
