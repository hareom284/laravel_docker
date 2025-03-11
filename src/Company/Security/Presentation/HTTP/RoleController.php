<?php

namespace Src\Company\Security\Presentation\HTTP;

use Src\Company\Security\Application\UseCases\Queries\Permissions\GetPermissionwithPagination;
use Src\Company\Security\Application\UseCases\Queries\Roles\GetRolewithPagniation;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\Security\Application\Requests\StoreRoleRequest;
use Src\Company\Security\Application\Requests\UpdateRoleRequest;
use Src\Company\Security\Infrastructure\EloquentModels\RoleEloquentModel;
use Src\Company\Security\Application\UseCases\Queries\Roles\GetRoleName;
use Inertia\Inertia;
use Src\Company\Security\Application\Policies\RolePolicy;
use Symfony\Component\HttpFoundation\Response;
use Src\Company\Security\Domain\Services\RoleService;

class RoleController extends Controller
{

    protected $roleSevice;

    public function __construct()
    {
        $this->roleSevice = app()->make(RoleService::class);
    }


    public function index()
    {
        // $this->authorize('view', RoleEloquentModel::class);

        abort_if(authorize('view', RolePolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Role!');


        try {
            // Check if the user is authorized to view roles

            // Get the filters from the request
            $filters = request()->only(['name', 'search', 'perPage']);


            // Retrieve roles with pagination using the provided filters
            $roles = (new GetRolewithPagniation($filters))->handle();

            // Retrieve role names
            $roles_name = (new GetRoleName())->handle();

            // Retrieve permissions with pagination
            $permissions = (new GetPermissionwithPagination([]))->handle();


            // Render the Inertia view with the obtained data
            return Inertia::render(config('route.roles'), [
                "roles" => $roles['paginate_roles'],
                "roles_name" => $roles_name,
                "permissions" => $permissions["default_permissions"]
            ]);
        } catch (\Exception $e) {
            return redirect()->route('roles.index')->with('sytemErrorMessage', $e->getMessage());
        }
    }


    /**
     * @param  StoreRoleRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     *
     */
    public function store(StoreRoleRequest $request)
    {
        // Check if the user is authorized to create roles

        abort_if(authorize('create', RolePolicy::class), Response::HTTP_FORBIDDEN, 'Need create permission for Role!');
        try {

            $this->roleSevice->createRole($request);
            // Redirect the user to the index page for roles with a success message
            return redirect()->route('roles.index')->with("successMessage", "Roles created Successfully!");
        } catch (\Exception $e) {
            return redirect()->route('roles.index')->with("sytemErrorMessage", $e->getMessage());
        }
    }

    /***
     * @param UpdateRoleRequest $request ,RoleEloquentModel $role
     *
     * @return \Illuminate\Http\RedirectResponse
     *
     */
    public function update(UpdateRoleRequest $request, RoleEloquentModel $role)
    {
        // Check if the user is authorized to edit roles
        abort_if(authorize('edit', RolePolicy::class), Response::HTTP_FORBIDDEN, 'Need edit permission for Role!');

        try {


            $this->roleSevice->updateRole($request, $role->id);

            // Redirect the user to the index page for roles with a success message
            return redirect()->route('roles.index')->with("successMessage", "Role updated Successfully!");
        } catch (\Exception $e) {

            return redirect()->route('roles.index')->with("sytemErrorMessage", $e->getMessage());
        }
    }


    //destroy role
    public function destroy(RoleEloquentModel $role)
    {
        abort_if(authorize('destroy', RolePolicy::class), Response::HTTP_FORBIDDEN, 'Need destroy permission for Role!');
        $this->roleSevice->deleteRole($role);
        return redirect()->route('roles.index')->with("successMessage", "Role deleted Successfully!");
    }
}
