<?php

namespace Src\Company\Security\Presentation\HTTP;

use Src\Common\Infrastructure\Laravel\Controller;
use Inertia\Inertia;
use Src\Company\Security\Application\UseCases\Queries\Users\GetUserName;
use Src\Company\Security\Application\UseCases\Queries\Roles\GetRoleName;

use Src\Company\Security\Application\Requests\StoreUserRequest;
use Src\Company\Security\Application\Requests\UpdateUserRequest;
use Src\Company\Security\Application\Requests\updateUserPasswordRequest;
use Src\Company\Security\Infrastructure\EloquentModels\UserEloquentModel;
use Src\Company\Security\Application\UseCases\Queries\Users\GetUsersWithPagination;
use Src\Company\Organization\Application\UseCases\Queries\GetOrganizatonName;
use Src\Company\Security\Application\Policies\UserPolicy;
use Src\Company\Security\Domain\Services\UserService;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{

    protected $userService;

    public function __construct()
    {
        $this->userService = app()->make(UserService::class);
    }

    /***
     *  @params null
     *
     *  @return \Illuminate\Http\RedirectResponse|\Inertia\Response
     *
     */
    public function index()
    {

        // return auth()->user()->load('organization');
        // Check if the user is authorized to view users

        abort_if(authorize('view', UserPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for User!');

        try {

            // Get the filters from the request, or initialize an empty array if they are not present
            $filters = request()->only(['name', 'email', 'role', 'search', 'perPage', 'roles']) ?? [];

            // Retrieve users with pagination using the provided filters
            $users = (new GetUsersWithPagination($filters))->handle();
            // Retrieve user names
            $users_name = (new GetUserName())->handle();

            // Retrieve role names
            $roles_name = (new GetRoleName())->handle();

            $oragnization_name  = (new GetOrganizatonName())->handle();


            // Render the Inertia view with the obtained data
            return Inertia::render(config('route.users'), [
                'users' => $users,
                'users_name' => $users_name,
                'roles_name' => $roles_name,
                "organizations" => $oragnization_name
            ]);
        } catch (\Exception $e) {
            return redirect()->route('users.index')->with('sytemErrorMessage', $e->getMessage());
        }
    }
    /**
     * Store a new user.
     *
     * @param StoreUserRequest $request The incoming request.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreUserRequest $request)
    {
        abort_if(authorize('create', UserPolicy::class), Response::HTTP_FORBIDDEN, 'Need create permission for User!');

        try {

            $this->userService->createUser($request);

            return redirect()->route('users.index')->with("successMessage", "User created successfully!");
        } catch (\Exception $e) {
            // Handle the exception, log the error, or display a user-friendly error message.
            return redirect()->route('users.index')->with("sytemErrorMessage", $e->getMessage());
        }
    }



    //update user
    public function update(UpdateUserRequest $request, UserEloquentModel $user)
    {
        abort_if(authorize('edit', UserPolicy::class), Response::HTTP_FORBIDDEN, 'Need edit permission for User!');
        $this->userService->updateUser($request, $user->id);
        return redirect()->route('users.index')->with("successMessage", "User Updated Successfully!");
    }


    public function destroy(UserEloquentModel $user)
    {
        abort_if(authorize('destroy', UserPolicy::class), Response::HTTP_FORBIDDEN, 'Need destroy permission for User!');
        $this->userService->deleteUser($user);
        return redirect()->route('users.index')->with("successMessage", "User Deleted Successfully!");
    }

    public function changePassword(updateUserPasswordRequest $request)
    {

        try {
            $isPasswordMatch = $this->userService->changePassword($request);
            if ($isPasswordMatch) {
                return redirect()->route('userprofile')->with("successMessage", "Password Updated Successfully!");
            }
            return redirect()->route('userprofile')->with("errorMessage", "Password does not match!");
        } catch (\Exception $error) {

            dd("something was wrong", $error->getMessage());
        }
    }
}
