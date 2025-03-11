<?php

namespace Src\Company\Project\Application\DTO;

use Illuminate\Http\Request;
use Src\Company\Project\Infrastructure\EloquentModels\TermAndConditionEloquentModel;

class TermAndConditionData
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $title,
    )
    {}

    public static function fromRequest(Request $request, ?int $team_id = null): TermAndConditionData
    {
        return new self(
            id: $team_id,
            title: $request->string('title'),
        );
    }

    public static function fromEloquent(TermAndConditionEloquentModel $termAndConditionEloquentModel): self
    {
        return new self(
            id: $termAndConditionEloquentModel->id,
            title: $termAndConditionEloquentModel->title,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title
        ];
    }
}
