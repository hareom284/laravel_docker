<?php

namespace Src\Company\Document\Presentation\API;

use Exception;
use Illuminate\Http\JsonResponse;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Symfony\Component\HttpFoundation\Response;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\Document\Application\Mappers\ProjectRequirementMapper;
use Src\Company\Document\Application\Requests\StoreProjectRequirementRequest;
use Src\Company\Document\Domain\Repositories\ProjectRequirementRepositoryInterface;
use Src\Company\Document\Application\Policies\ProjectRequirementPolicy;
use Src\Company\Document\Application\Requests\UpdateProjectRequirementRequest;
use Src\Company\Document\Application\UseCases\Commands\DeleteProjectRequirementMobileCommand;
use Src\Company\Document\Application\UseCases\Commands\StoreProjectRequirementMobileCommand;
use Src\Company\Document\Application\UseCases\Commands\UpdateProjectRequirementMobileCommand;
use Src\Company\Document\Application\UseCases\Queries\FindProjectRequirementByIdMobileQuery;
use Src\Company\Document\Application\UseCases\Queries\GetProjectRequirementMobileQuery;

class ProjectRequirementMobileController extends Controller
{
    private $projectRequirementInterFace;

    public function __construct(ProjectRequirementRepositoryInterface $projectRequirementRepository)
    {
        $this->projectRequirementInterFace = $projectRequirementRepository;
    }

    public function index(int $project_id): JsonResponse
    {
        //check if user's has permission
        // abort_if(authorize('view', ProjectRequirementPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Project Requirement!');

        try {

            $projectRequirements =  (new GetProjectRequirementMobileQuery($project_id))->handle();

            return response()->success($projectRequirements, 'success', Response::HTTP_OK);
        } catch (Exception $ex) {

            return response()->error($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(int $id): JsonResponse
    {

        try {

            $projectRequirement = (new FindProjectRequirementByIdMobileQuery($id))->handle();
            return response()->success($projectRequirement, 'success', Response::HTTP_OK);
        } catch (Exception $ex) {

            return response()->json(['error' => $ex->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(StoreProjectRequirementRequest $request): JsonResponse
    {

        try {
            $projectRequirement = ProjectRequirementMapper::fromRequest($request);

            $requirementData = (new StoreProjectRequirementMobileCommand($projectRequirement))->execute();

            return response()->success($requirementData, 'success', Response::HTTP_CREATED);
        } catch (Exception $ex) {

            return response()->error($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        } 
    }

    public function update(int $id, UpdateProjectRequirementRequest $request): JsonResponse
    {
        //check if user's has permission
        abort_if(authorize('update', ProjectRequirementPolicy::class), Response::HTTP_FORBIDDEN, 'Need update permission for Project Requirement!');

        try {

            $projectRequirement = ProjectRequirementMapper::fromRequest($request, $id);

            $requirementData = (new UpdateProjectRequirementMobileCommand($projectRequirement, $request))->execute();

            return response()->success($requirementData, 'success', Response::HTTP_OK);

        } catch (Exception $ex) {

            return response()->error($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        } 
    }

    public function destroy(int $id): JsonResponse
    {

        try {

            (new DeleteProjectRequirementMobileCommand($id))->execute();
            return response()->success($id, "Successfully Deleted", Response::HTTP_OK);
        } catch (Exception $ex) {

            return response()->error($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
