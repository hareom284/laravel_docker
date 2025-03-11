<?php

namespace Src\Company\Project\Application\Mappers;

use Illuminate\Http\Request;
use Src\Company\Project\Domain\Model\Entities\Project;
use Src\Company\Project\Infrastructure\EloquentModels\ProjectEloquentModel;
use Src\Company\Project\Application\DTO\ProjectData;

class ProjectMapper
{

    public static function fromRequest(Request $request, ?int $project_id = null): Project
    {
        return new Project(
            id: $project_id,
            invoice_no: $request->string('invoice_no'),
            description: $request->string('description'),
            collection_of_keys: $request->string('collection_of_keys'),
            expected_date_of_completion: $request->string('expected_date_of_completion'),
            // completed_date: $request->string('completed_date'),
            project_status: $request->string('project_status'),
            customer_id: $request->customer_id,
            property_id: $request->integer('property_id') ?? null,
            company_id: $request->integer('company_id'),
            payment_status: $request->string('payment_status'),
            request_note: $request->string('request_note'),
            term_and_condition_id: $request->term_and_condition_id ?? null
        );
    }

    public static function toEloquent(Project $project, ?int $property_id = null, ?string $agreement_no = null): ProjectEloquentModel
    {
        $projectEloquent = new ProjectEloquentModel();
        if ($project->id) {
            $projectEloquent = ProjectEloquentModel::query()->findOrFail($project->id);
            $projectEloquent->description = $project->description;
            $projectEloquent->collection_of_keys = $project->collection_of_keys == '' ? null : $project->collection_of_keys;
            $projectEloquent->expected_date_of_completion = $project->expected_date_of_completion == '' ? null : $project->expected_date_of_completion;
            $projectEloquent->customer_id = $project->customer_id;
            $projectEloquent->company_id = $project->company_id;
            $projectEloquent->collection_of_keys = $project->collection_of_keys == '' ? null : $project->collection_of_keys;
            $projectEloquent->expected_date_of_completion = $project->expected_date_of_completion == '' ? null : $project->expected_date_of_completion;
            $projectEloquent->completed_date = null;

        } else {
            $projectEloquent->invoice_no = $project->invoice_no;
            $projectEloquent->description = $project->description;
            $projectEloquent->completed_date = null;
            $projectEloquent->project_status = "New";
            $projectEloquent->company_id = $project->company_id;
            $projectEloquent->customer_id = $project->customer_id;
            $projectEloquent->property_id = $property_id;
            $projectEloquent->agreement_no = $agreement_no;
            $projectEloquent->created_by = auth('sanctum')->user()->id;
        }

        $projectEloquent->payment_status = !empty($project->payment_status) ? $project->payment_status : 'DEFAULT';
        $projectEloquent->request_note = $project->request_note;
        $projectEloquent->term_and_condition_id = $project->term_and_condition_id ?? null;

        return $projectEloquent;
    }

    public static function fromEloquent(ProjectEloquentModel $projectEloquent): ProjectData
    {
        return new ProjectData(
            id: $projectEloquent->id,
            invoice_no: $projectEloquent->invoice_no,
            description: $projectEloquent->description,
            collection_of_keys: $projectEloquent->collection_of_keys ?? '',
            expected_date_of_completion: $projectEloquent->expected_date_of_completion ?? '',
            completed_date: $projectEloquent->completed_date,
            project_status: $projectEloquent->project_status,
            customer_id: $projectEloquent->customer_id,
            company_id: $projectEloquent->company_id,
            payment_status: $projectEloquent->payment_status,
            request_note: $projectEloquent->request_note,
            term_and_condition_id: $projectEloquent->term_and_condition_id
        );
    }
}
