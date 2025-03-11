<?php

namespace Src\Company\UserManagement\Presentation\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Src\Company\UserManagement\Application\Mappers\RoleMapper;
use Src\Company\UserManagement\Application\UseCases\Commands\DeleteRoleCommand;
use Src\Company\UserManagement\Application\UseCases\Commands\UpdateRoleCommand;
use Symfony\Component\HttpFoundation\Response;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\UserManagement\Application\Requests\StoreRoleRequest;
use Src\Company\UserManagement\Application\Policies\RolePolicy;
use Src\Company\UserManagement\Application\UseCases\Commands\StoreRoleCommand;
use Src\Company\UserManagement\Application\UseCases\Queries\FindRoleByIdQuery;
use Src\Company\UserManagement\Domain\Repositories\RoleRepositoryInterface;

class RoleController extends Controller
{
    private $roleInterFace;

    public function __construct(RoleRepositoryInterface $roleRepository)
    {
        $this->roleInterFace = $roleRepository;
    }

    public function index(Request $request): JsonResponse
    {
        //check if user's has permission
        // abort_if(authorize('view', RolePolicy::class), Response::HTTP_FORBIDDEN, '403 Forbidden');

        try {

            $filters = $request;

            $roles = $this->roleInterFace->getRoles($filters);

            return response()->success($roles, 'success', Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function show(int $id): JsonResponse
    {
        abort_if(authorize('view', RolePolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Role!');

        try {
            return response()->json((new FindRoleByIdQuery($id))->handle());
        } catch (UnauthorizedUserException $e) {

            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function store(StoreRoleRequest $request): JsonResponse
    {
        //check if user's has permission
        abort_if(authorize('store', RolePolicy::class), Response::HTTP_FORBIDDEN, 'Need store permission for Role!');

        try {
            $role = RoleMapper::fromRequest($request);

            $permissionIds = $request->permission_ids;

            $roleData = (new StoreRoleCommand($role, $permissionIds))->execute();

            if ($roleData == false) {
                return response()->error("Role with permissions aleady created!", Response::HTTP_CONFLICT);
            }

            return response()->success($roleData, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function update(int $id, Request $request): JsonResponse
    {
        //check if user's has permission
        abort_if(authorize('update', RolePolicy::class), Response::HTTP_FORBIDDEN, 'Need update permission for Role!');

        try {
            $role = RoleMapper::fromRequest($request, $id);

            $permissionIds = $request->permission_ids;

            (new UpdateRoleCommand($role, $permissionIds))->execute();

            return response()->json($role, Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->json($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        //check if user's has permission
        abort_if(authorize('destroy', RolePolicy::class), Response::HTTP_FORBIDDEN, 'Need destroy permission for Role!');

        try {
            (new DeleteRoleCommand($id))->execute();

            return response()->success($id, "Deleted Successfully", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }
}
