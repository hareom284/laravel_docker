<?php

namespace Src\Company\CustomerManagement\Presentation\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Symfony\Component\HttpFoundation\Response;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\CustomerManagement\Application\Mappers\IdMilestoneMapper;
use Src\Company\CustomerManagement\Application\UseCases\Commands\DeleteIdMilestoneCommand;
use Src\Company\CustomerManagement\Application\UseCases\Commands\StoreIdMilestonesCommand;
use Src\Company\CustomerManagement\Application\UseCases\Commands\UpdateIdMilestoneOrderCommand;
use Src\Company\CustomerManagement\Application\UseCases\Commands\UpdateIdMilestonesDataCommand;
use Src\Company\CustomerManagement\Application\UseCases\Queries\FindAllIdMilestoneActionsQuery;
use Src\Company\CustomerManagement\Application\UseCases\Queries\FindAllIdMilestonesQuery;
use Src\Company\CustomerManagement\Application\UseCases\Queries\FindIdMilestonesQuery;
use Src\Company\CustomerManagement\Application\UseCases\Queries\GetAllWhatsappTemplatesQuery;

class IdMilestoneController extends Controller
{

    public function index(): JsonResponse
    {
        try {

            return response()->success((new FindAllIdMilestonesQuery())->handle(), "Id Milestone List", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function show($id): JsonResponse
    {
        try {

            return response()->success((new FindIdMilestonesQuery($id))->handle(), "Id Milestone Detail", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function idMilestoneActions(): JsonResponse
    {
        try {

            return response()->success((new FindAllIdMilestoneActionsQuery())->handle(), "Id Milestone List", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {

            $idMilestone = IdMilestoneMapper::fromRequest($request);

            $idMilestoneData = (new StoreIdMilestonesCommand($idMilestone))->execute();

            return response()->success($idMilestoneData, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function update(int $id, Request $request): JsonResponse
    {
        try {
            $idMilestone = IdMilestoneMapper::fromRequest($request, $id);

            $idMilestoneData = (new UpdateIdMilestonesDataCommand($idMilestone))->execute();

            return response()->success($idMilestoneData, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->json($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function orderUpdate(Request $request)
    {
        try {
            $idMilestone = $request->idMilestones;

            $idMilestoneData = (new UpdateIdMilestoneOrderCommand($idMilestone))->execute();

            return response()->success($idMilestoneData, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->json($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            (new DeleteIdMilestoneCommand($id))->execute();

            return response()->success($id, "Deleted Successfully", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function getWhatsappTemplates()
    {
        try {
            return response()->success((new GetAllWhatsappTemplatesQuery())->handle(), "Whatsapp Templates", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }
}
