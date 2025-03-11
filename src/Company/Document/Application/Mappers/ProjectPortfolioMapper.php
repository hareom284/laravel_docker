<?php

namespace Src\Company\Document\Application\Mappers;

use Illuminate\Http\Request;
use Src\Company\Document\Domain\Model\Entities\ProjectPortfolio;
use Src\Company\Document\Infrastructure\EloquentModels\ProjectPorfolioEloquentModel;

class ProjectPortfolioMapper
{
    public static function fromRequest(Request $request, ?int $id = null): ProjectPortfolio
    {

        $getField = function (string $field, string $type) use ($request) {
            return $request->filled($field) ? $request->{$type}($field) : null;
        };

        return new ProjectPortfolio(
            id: $id,
            project_id: $getField('project_id', 'integer'),
            title: $getField('title', 'string'),
            description: $getField('description', 'string'),
        );
    }

    public static function fromEloquent(ProjectPorfolioEloquentModel $projectPorfolio): ProjectPortfolio
    {
        return new ProjectPortfolio(
            id: $projectPorfolio->id,
            project_id: $projectPorfolio->project_id,
            title: $projectPorfolio->title,
            description: $projectPorfolio->description,
        );
    }

    public static function toEloquent(ProjectPortfolio $projectPorfolio): ProjectPorfolioEloquentModel
    {
        $projectPortfolioEloquent = new ProjectPorfolioEloquentModel();
        if ($projectPorfolio->id) {
            $projectPortfolioEloquent = ProjectPorfolioEloquentModel::query()->findOrFail($projectPorfolio->id);
        }
        $projectPortfolioEloquent->project_title = $projectPorfolio->project_id;
        $projectPortfolioEloquent->title = $projectPorfolio->title;
        $projectPortfolioEloquent->description = $projectPorfolio->description;
        return $projectPortfolioEloquent;
    }
}
