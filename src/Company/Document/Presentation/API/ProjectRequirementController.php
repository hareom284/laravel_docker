<?php

namespace Src\Company\Document\Presentation\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Src\Company\Document\Application\Mappers\ProjectRequirementMapper;
use Src\Company\Document\Application\UseCases\Commands\StoreProjectRequirementCommand;
use Src\Company\Document\Application\UseCases\Commands\DeleteProjectRequirementCommand;
use Src\Company\Document\Application\UseCases\Commands\UpdateProjectRequirementCommand;
use Src\Company\Document\Application\UseCases\Queries\FindProjectRequirementByIdQuery;
use Symfony\Component\HttpFoundation\Response;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\Document\Application\Requests\StoreProjectRequirementRequest;
use Src\Company\Document\Domain\Repositories\ProjectRequirementRepositoryInterface;
use Src\Company\Document\Application\Policies\ProjectRequirementPolicy;
use Src\Company\Document\Application\Requests\UpdateProjectRequirementRequest;
use Src\Company\Document\Domain\Resources\ProjectRequirementResource;
use Src\Company\Document\Infrastructure\EloquentModels\ProjectRequirementEloquentModel;

class ProjectRequirementController extends Controller
{
    private $projectRequirementInterFace;

    public function __construct(ProjectRequirementRepositoryInterface $projectRequirementRepository)
    {
        $this->projectRequirementInterFace = $projectRequirementRepository;
    }

    public function index(int $project_id): JsonResponse
    {
        //check if user's has permission
        abort_if(authorize('view', ProjectRequirementPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Project Requirement!');

        try {

            $projectRequirements = $this->projectRequirementInterFace->getProjectRequirements($project_id);

            return response()->success($projectRequirements, 'success', Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function show(int $id): JsonResponse
    {
        //check if user's has permission
        abort_if(authorize('view', ProjectRequirementPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Project Requirement!');

        try {

            $requirement = new ProjectRequirementResource((new FindProjectRequirementByIdQuery($id))->handle());

            return response()->success($requirement, 'success', Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function store(StoreProjectRequirementRequest $request): JsonResponse
    {
        //check if user's has permission
        abort_if(authorize('store', ProjectRequirementPolicy::class), Response::HTTP_FORBIDDEN, 'Need store permission for Project Requirement!');

        try {
            $projectRequirement = ProjectRequirementMapper::fromRequest($request);

            $requirementData = (new StoreProjectRequirementCommand($projectRequirement))->execute();

            return response()->success($requirementData, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function update(int $id, UpdateProjectRequirementRequest $request): JsonResponse
    {
        //check if user's has permission
        abort_if(authorize('update', ProjectRequirementPolicy::class), Response::HTTP_FORBIDDEN, 'Need update permission for Project Requirement!');

        try {
            // $requirement = ProjectRequirementMapper::fromRequest($request, $id);

            $requirementEloquent = ProjectRequirementEloquentModel::query()->where('id',$id)->first();

            if($request->hasFile('document_file')) {

                $originalName = pathinfo($request->document_file->getClientOriginalName(), PATHINFO_FILENAME);
                $originalName = preg_replace("/[^a-zA-Z0-9\s]/", "", $originalName);
                $originalName = substr($originalName, 0, 200); // Limiting the length to avoid very long filenames

                // Generate a unique suffix to append
                $timestamp = time();
                $uniqueId = uniqid();
                $extension = $request->document_file->getClientOriginalExtension();
                $fileTitle = "{$originalName}_{$timestamp}_{$uniqueId}.{$extension}";

                $filePath = 'project_requirement/' . $fileTitle;

                Storage::disk('public')->put($filePath, file_get_contents($request->document_file));

                $documentFile = $fileTitle;
            } else {
                // Set null if don't exists
                $documentFile = $request->original_document ? $request->original_document : null;
            }

            $requirementEloquent->document_file = $documentFile;
            $requirementEloquent->title = $request->title;
            $requirementEloquent->project_id = $request->project_id;
            $requirementEloquent->update();

            return response()->success($requirementEloquent, 'success', Response::HTTP_OK);

        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        //check if user's has permission
        abort_if(authorize('destroy', ProjectRequirementPolicy::class), Response::HTTP_FORBIDDEN, 'Need destroy permission for Project Requirement!');

        try {
            (new DeleteProjectRequirementCommand($id))->execute();

            return response()->success($id, "Successfully Deleted", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }
}
