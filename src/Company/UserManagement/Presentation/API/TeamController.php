<?php

namespace Src\Company\UserManagement\Presentation\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Symfony\Component\HttpFoundation\Response;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\UserManagement\Application\Mappers\TeamMapper;
use Src\Company\UserManagement\Application\Policies\TeamPolicy;
use Src\Company\UserManagement\Application\UseCases\Commands\DeleteTeamCommand;
use Src\Company\UserManagement\Application\UseCases\Commands\StoreTeamCommand;
use Src\Company\UserManagement\Application\UseCases\Commands\UpdateTeamCommand;
use Src\Company\UserManagement\Application\UseCases\Queries\FindAllTeamQuery;
use Src\Company\UserManagement\Application\UseCases\Queries\FindTeamByIdQuery;

class TeamController extends Controller
{

    public function index(Request $request): JsonResponse
    {
        //check if user's has permission
        abort_if(authorize('view', TeamPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Team!');

        try {

            $filters = $request->all();

            return response()->success((new FindAllTeamQuery($filters))->handle(), "Team List", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function store(Request $request): JsonResponse
    {
        abort_if(authorize('store', TeamPolicy::class), Response::HTTP_FORBIDDEN, 'Need store permission for Team!');

        try {

            $teamMembers = json_decode($request->team_member_ids);

            $team = TeamMapper::fromRequest($request);

            $teamData = (new StoreTeamCommand($team,$teamMembers))->execute();

            return response()->success($teamData, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function update(Request $request): JsonResponse
    {
        abort_if(authorize('update', TeamPolicy::class), Response::HTTP_FORBIDDEN, 'Need update permission for Team!');

        try {

            $teamMembers = json_decode($request->team_member_ids);

            $team = TeamMapper::fromRequest($request, $request->id);

            (new UpdateTeamCommand($team,$teamMembers))->execute();

            return response()->success(null, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function show(int $id)
    {
        abort_if(authorize('view', TeamPolicy::class), Response::HTTP_FORBIDDEN, 'Need update permission for Team!');

        try {

            return response()->success((new FindTeamByIdQuery($id))->handle(), "Team List", Response::HTTP_OK);

        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        //check if user's has permission
        abort_if(authorize('destroy', TeamPolicy::class), Response::HTTP_FORBIDDEN, 'Need destroy permission for Team!');

        try {

            (new DeleteTeamCommand($id))->execute();

            return response()->success($id, "Deleted Successfully", Response::HTTP_OK);

        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }
}
