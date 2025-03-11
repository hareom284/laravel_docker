<?php

namespace Src\Company\UserManagement\Presentation\API;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\System\Application\Policies\LeadPolicy;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Src\Company\UserManagement\Application\Mappers\UserMapper;
use Src\Company\UserManagement\Application\Policies\UserPolicy;
use Src\Company\UserManagement\Domain\Imports\UserUpdateImport;
use Src\Company\UserManagement\Domain\Model\ValueObjects\Password;
use Src\Company\UserManagement\Application\Requests\StoreUserRequest;
use Src\Company\UserManagement\Application\Requests\UpdateUserRequest;
use Src\Company\UserManagement\Application\Requests\UpdateProfileRequest;
use Src\Company\CustomerManagement\Application\Requests\CreateLeadRequest;
use Src\Company\CustomerManagement\Application\Requests\UpdateLeadRequest;
use Src\Company\System\Application\UseCases\Commands\UpdateCustomerCommand;
use Src\Company\UserManagement\Domain\Repositories\UserRepositoryInterface;
use Src\Company\Project\Infrastructure\EloquentModels\PropertyEloquentModel;
use Src\Company\UserManagement\Application\UseCases\Queries\GetUserListQuery;
use Src\Company\System\Infrastructure\EloquentModels\SiteSettingEloquentModel;
use Src\Company\UserManagement\Application\UseCases\Commands\StoreUserCommand;
use Src\Company\CustomerManagement\Application\Requests\StoreVendorUserRequest;
use Src\Company\UserManagement\Application\UseCases\Commands\DeleteUserCommand;
use Src\Company\UserManagement\Application\UseCases\Queries\GetManagerListQuery;
use Src\Company\StaffManagement\Infrastructure\EloquentModels\StaffEloquentModel;
use Src\Company\UserManagement\Application\UseCases\Commands\UpdateProfileCommand;
use Src\Company\UserManagement\Application\UseCases\Queries\GetSurveyByUserIdQuery;
use Src\Company\UserManagement\Application\UseCases\Commands\UpdateStaffUserCommand;
use Src\Company\UserManagement\Application\UseCases\Queries\GetTeamMembersListQuery;
use Src\Company\UserManagement\Application\UseCases\Queries\FindUserResourceByIdQuery;
use Src\Company\UserManagement\Application\UseCases\Queries\GetSelectboxUserListQuery;
use Src\Company\UserManagement\Application\UseCases\Commands\UpdateCustomerUserCommand;
use Src\Company\CustomerManagement\Application\UseCases\Commands\SendSuccessCreateLeadMailToCustomerCommand;

class UserController extends Controller
{
    private $userInterFace;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userInterFace = $userRepository;
    }

    public function index(Request $request): JsonResponse
    {
        abort_if(authorize('view', UserPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for User!');

        try {

            $filters = $request->all();

            return response()->success((new GetUserListQuery($filters))->handle(), "User Lists", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function show($id)
    {
        abort_if(authorize('view', UserPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for User!');

        try {
            return response()->success((new FindUserResourceByIdQuery($id))->handle(), "User", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function createStaffUser(StoreUserRequest $request): JsonResponse
    {
        abort_if(authorize('store', UserPolicy::class), Response::HTTP_FORBIDDEN, 'Need store permission for User!');

        try {

            $user = UserMapper::fromRequest($request);

            $roleIds = $request->role_ids;

            // $staff = in_array(1, $roleIds) ? StaffMapper::fromRequest($request) : null;

            $password = new Password($request->input('password'), $request->input('password_confirmation'));

            $userData = (new StoreUserCommand($user, $password, $roleIds, null, null, null))->execute();

            return response()->success($userData, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function updateStaffUser(int $id, UpdateUserRequest $request): JsonResponse
    {
        abort_if(authorize('update', UserPolicy::class), Response::HTTP_FORBIDDEN, 'Need update permission for User!');

        try {
            $roleIds = $request->role_ids;

            $user = UserMapper::fromRequest($request, $id);

            // if (in_array(5, $roleIds)) {
            //     $staff = null;
            // } else {
            //     $staff = StaffMapper::fromRequest($request);
            // }

            (new UpdateStaffUserCommand($user, $roleIds, null, null))->execute();

            return response()->success($request->role_ids, 'success', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function createCustomerUser(CreateLeadRequest $request)
    {
        //check if user's has permission
        abort_if(authorize('store', LeadPolicy::class), Response::HTTP_FORBIDDEN, 'Need store permission for User!');

        try {
            $properties = json_decode($request->properties) ?? [];

            $salespersonIds = json_decode($request->saleperson_ids) ?? [];
           
            $salespersonNames = [];

            // Checking Condition for Potential Error of Staff Information not being stored in staff table
            foreach ($salespersonIds as $value) {

                $staff_info = StaffEloquentModel::query()->where('user_id', $value)->first();

                if ($staff_info) {
                    $salespersonName = $staff_info->user->first_name . ' ' . $staff_info->user->last_name;
                    // Push each salesperson name into the array
                    $salespersonNames[] = $salespersonName;
                } else {
                    return response()->error(['sale_rank' => "Cannot create lead because required saleperson info (Rank) doesn't existed."], "Saleperson Info Not Sufficient", 400);
                }
            }

            // Checking For Property Duplicate Error
            $propertyLists = PropertyEloquentModel::query()->get();

            $duplicateProperty = [];

            foreach ($properties as $value) {
                if ($value->postal_code || $value->unit_num) {

                    $data = $propertyLists->contains(function ($property) use ($value) {
                        return $property->postal_code == $value->postal_code && $property->unit_num == $value->unit_num;
                    });

                    if ($data) {
                        array_push($duplicateProperty, $value);
                    }
                }
            }

            if ($request->create_account == "true") {

                $randomString = Str::random(8);

                $password = new Password($randomString, $randomString);
            } else {
                $password = null;
            }

            $user = UserMapper::fromRequest($request);

            logger('message',[$user]);            // $customer = CustomerMapper::fromRequest($request);

            $roleIds = $request->role_ids;

            $userData = (new StoreUserCommand($user, $password, $roleIds, $salespersonIds))->execute();

            $customerName = $request->first_name . ' ' . $request->last_name;

            $siteSetting = SiteSettingEloquentModel::first();

            if ($request->create_account == "true") {
                // Use "$randomString" instead of the old "$password->value" in an attempt to solve the issue where email contains encrypted password, instead of actual password
                (new SendSuccessCreateLeadMailToCustomerCommand($customerName, $request->email, $randomString, $siteSetting, $salespersonNames))->execute();
            }

            return response()->success($userData, "Success", Response::HTTP_CREATED,);

        } catch (\DomainException $domainException) {
            return response()->json(['error' => $domainException->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function createVendorUser(StoreVendorUserRequest $request)
    {
        //check if user's has permission
        abort_if(authorize('store', LeadPolicy::class), Response::HTTP_FORBIDDEN, 'Need store permission for User!');

        try {

            DB::beginTransaction();

            if ($request->create_account == "true") {

                $randomString = Str::random(8);

                $password = new Password($randomString, $randomString);

            } else {

                $password = null;
            }

            $user = UserMapper::fromRequest($request);

            $roleIds = $request->role_ids ??  [];

            $userData = (new StoreUserCommand($user, $password, $roleIds,null,null,null))->execute();

            $customerName = $request->first_name . ' ' . $request->last_name;

            $siteSetting = SiteSettingEloquentModel::first();

            $salespersonNames = [];

            if ($request->create_account == "true") {
                (new SendSuccessCreateLeadMailToCustomerCommand($customerName, $request->email, $randomString, $siteSetting, $salespersonNames))->execute();
            }

            DB::commit();

            return response()->success($userData, "Success", Response::HTTP_CREATED,);



        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json(['error' => $exception->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function updateCustomerUser(int $id, UpdateLeadRequest $request): JsonResponse
    {
        //check if user's has permission
        abort_if(authorize('update', LeadPolicy::class), Response::HTTP_FORBIDDEN, 'Need update permission for User!');

        try {

            $properties = json_decode($request->properties) ?? [];

            $filteredData = array_filter($properties, function ($item) {
                return $item->property_id != "";
            });

            $filteredData = array_values($filteredData);

            $propertyIds = array_map(function ($item) {
                return $item->property_id;
            }, $filteredData);

            // return response()->error($propertyIds,"Saleperson Info Not Sufficient",422);

            $propertyLists = PropertyEloquentModel::query()->get();

            $propertyList2 = PropertyEloquentModel::query()->whereNotIn('id', $propertyIds)->get();

            $duplicateProperty = [];

            foreach ($properties as $value) {

                if ($value->property_id == null) {
                    if ($value->postal_code || $value->unit_num) {

                        $data = $propertyLists->contains(function ($property) use ($value) {
                            return $property->postal_code == $value->postal_code && $property->unit_num == $value->unit_num;
                        });

                        if ($data) {
                            array_push($duplicateProperty, $value);
                        }
                    }
                } else {
                    if ($value->postal_code || $value->unit_num) {

                        $data = $propertyList2->contains(function ($property) use ($value) {
                            return $property->postal_code == $value->postal_code && $property->unit_num == $value->unit_num;
                        });

                        if ($data) {
                            array_push($duplicateProperty, $value);
                        }
                    }
                }
            }


            $salespersonIds = json_decode($request->saleperson_ids) ?? [];


            $salespersonNames = [];

            // Checking Condition for Potential Error of Staff Information not being stored in staff table
            foreach ($salespersonIds as $value) {

                $staff_info = StaffEloquentModel::query()->where('user_id', $value)->first();

                if ($staff_info) {
                    $salespersonName = $staff_info->user->first_name . ' ' . $staff_info->user->last_name;
                    // Push each salesperson name into the array
                    $salespersonNames[] = $salespersonName;
                } else {
                    return response()->error(['sale_rank' => "Cannot create lead because required saleperson info (Rank) doesn't existed."], "Saleperson Info Not Sufficient", 400);
                }
            }

            if ($request->create_account == "true") {

                $randomString = Str::random(8);

                $password = new Password($randomString, $randomString);
            } else {
                $password = null;
            }

            $user = (new UpdateCustomerUserCommand($id, $request->all(), $password))->execute();

            $customerName = $request->first_name . ' ' . $request->last_name;

            $siteSetting = SiteSettingEloquentModel::first();

            if ($request->create_account == "true") {
                // Use "$randomString" instead of the old "$password->value" in an attempt to solve the issue where email contains encrypted password, instead of actual password
                (new SendSuccessCreateLeadMailToCustomerCommand($customerName, $request->email, $randomString, $siteSetting, $salespersonNames))->execute();
            }

            return response()->success($user, "Success", Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function updateProfile(int $id, UpdateProfileRequest $request): JsonResponse
    {
        abort_if(authorize('update_profile', UserPolicy::class), Response::HTTP_FORBIDDEN, 'Need update_profile permission for User!');

        try {

            $user = $request->all();

            if ($request->has('password') && $request->input('password')) {

                $password = new Password($request->input('password'), $request->input('password_confirmation'));

                $updatedUser = (new UpdateProfileCommand($user, $password, $id))->execute();
            } else {
                // Perform the update without updating the password
                $updatedUser = (new UpdateProfileCommand($user, null, $id))->execute();
            }

            if ($updatedUser->profile_pic) {
                $profile_pic_file_path = 'profile_pic/' . $updatedUser->profile_pic;

                $profile_pic_image = Storage::disk('public')->get($profile_pic_file_path);

                $updatedUser->profile_pic = base64_encode($profile_pic_image);
            }

            return response()->success($updatedUser, 'success', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        abort_if(authorize('destroy', UserPolicy::class), Response::HTTP_FORBIDDEN, 'Need destroy permission for User!');

        try {
            (new DeleteUserCommand($id))->execute();
            $user_id = $id;
            return response()->success($user_id, "Successfully Deleted", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {
            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function selectBoxUsers()
    {
        try {
            return response()->success((new GetSelectboxUserListQuery())->handle(), "User Lists For Selectbox", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function getManagers()
    {
        try {
            return response()->success((new GetManagerListQuery())->handle(), "Manager List", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function getTeamMembers()
    {
        try {
            return response()->success((new GetTeamMembersListQuery())->handle(), "Team Memebers List", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function userExcelUpdateImport(Request $request)
    {
        try {

            Excel::import(new UserUpdateImport, request()->file('user_excel'));

            return response()->success(null, "Successfully Excel Imported", Response::HTTP_OK);

        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function getSurveyByUserId($id)
    {
        try {
            return response()->success((new GetSurveyByUserIdQuery($id))->handle(), "Survey List", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }
}
