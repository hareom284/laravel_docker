<?php

namespace Src\Company\Document\Application\DTO;

use Illuminate\Http\Request;

use Src\Company\Document\Infrastructure\EloquentModels\ProjectPorfolioEloquentModel;

class ProjectPortfolioData
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $project_id,
        public readonly string $title,
        public readonly string $description,

    ) {}

    public static function fromRequest(Request $request, ?int $id = null): ProjectPortfolioData
    {
        return new self(
            id: $id,
            project_id: $request->integer('project_id'),
            title: $request->string('title'),
            description: $request->string('description'),
        );
    }

    public static function fromEloquent(ProjectPorfolioEloquentModel $projectPorfolio): self
    {
        return new self(
            id: $projectPorfolio->id,
            project_id: $projectPorfolio->project_id,
            title: $projectPorfolio->title,
            description: $projectPorfolio->description
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'project_id' => $this->project_id,
            'title' => $this->title,
            'description' => $this->description
        ];
    }
}
