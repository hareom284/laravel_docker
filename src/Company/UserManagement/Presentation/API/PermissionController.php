<?php

namespace Src\Company\UserManagement\Presentation\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Symfony\Component\HttpFoundation\Response;
use Src\Company\UserManagement\Domain\Repositories\PermissionRepositoryInterface;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\UserManagement\Application\UseCases\Queries\FindAllPermissionQuery;

class PermissionController extends Controller
{
    private $permissionInterFace;

    public function __construct(PermissionRepositoryInterface $permissionRepository)
    {
        $this->permissionInterFace = $permissionRepository;
    }

    public function index(Request $request): JsonResponse
    {
        try {

            $filters = $request;

            $permissions = $this->permissionInterFace->getPermissions($filters);

            return response()->success($permissions,'success',Response::HTTP_OK);

        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function permissionList(): JsonResponse
    {
        try {

            $permissions = (new FindAllPermissionQuery())->handle();

            return response()->success($permissions,'success',Response::HTTP_OK);

        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

}
