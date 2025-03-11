<?php

namespace Src\Company\CustomerManagement\Presentation\API;

use Exception;
use Illuminate\Http\JsonResponse;
use Src\Common\Infrastructure\Laravel\Controller;
use Symfony\Component\HttpFoundation\Response;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Src\Company\CustomerManagement\Application\UseCases\Queries\FindCustomerBySalepersonIdMobileQuery;
use Src\Company\StaffManagement\Infrastructure\EloquentModels\StaffEloquentModel;
use Illuminate\Support\Str;
use Src\Company\CustomerManagement\Application\Mappers\CustomerMapper;
use Src\Company\CustomerManagement\Application\Policies\CustomerPolicy;
use Src\Company\CustomerManagement\Application\UseCases\Commands\InactiveCustomerMobileCommand;
use Src\Company\CustomerManagement\Application\UseCases\Commands\SendSuccessCreateLeadMailToCustomerCommand;
use Src\Company\CustomerManagement\Application\UseCases\Commands\StoreCustomerCommandMobile;
use Src\Company\CustomerManagement\Application\UseCases\Commands\UpdateCustomerCommand;
use Src\Company\CustomerManagement\Application\UseCases\Commands\UpdateCustomerCommandMobile;
use Src\Company\CustomerManagement\Application\UseCases\Queries\FindCustomerByIdMobileQuery;
use Src\Company\CustomerManagement\Application\UseCases\Queries\FindCustomerListWithPropertiesMobileQuery;
use Src\Company\Project\Application\Mappers\PropertyMapper;
use Src\Company\Project\Application\UseCases\Commands\StorePropertyCommand;
use Src\Company\Project\Application\UseCases\Commands\StorePropertyCommandMobile;
use Src\Company\Project\Infrastructure\EloquentModels\PropertyTypeEloquentModel;
use Src\Company\System\Application\Policies\LeadPolicy;
use Src\Company\System\Infrastructure\EloquentModels\SiteSettingEloquentModel;
use Src\Company\UserManagement\Application\Mappers\UserMapper;
use Src\Company\UserManagement\Application\UseCases\Commands\StoreUserCommandMobile;
use Src\Company\UserManagement\Application\UseCases\Commands\UpdateCustomerUserCommand;
use Src\Company\UserManagement\Application\UseCases\Commands\UpdateCustomerUserCommandMobile;
use Src\Company\UserManagement\Domain\Model\ValueObjects\Password;

class CustomerMobileController extends Controller
{
    public function getCustomerLists(Request $request): JsonResponse
    {
        try {

            $filters = $request->all();

            $viewType = $request->filled('viewType') ? $request->viewType : 'salesperson';

            $loginUser = auth('sanctum')->user();

            if ($viewType == 'salesperson') {
                $staff_info = StaffEloquentModel::query()->where('user_id', $loginUser->id)->first();

                if (!$staff_info) {
                    return response()->error(['sale_rank' => "Cannot show lead list because require saleperson info (Rank) doesn't existed."], "Saleperson Info Not Sufficient", 400);
                }
            }

            $leadLists = "";

            if ($viewType == 'management' || $viewType == 'marketing') {
                if (isset($filters['saleperson_id'])) {
                    $leadLists = (new FindCustomerBySalepersonIdMobileQuery($filters['saleperson_id'], $filters))->handle();
                } else {
                    $leadLists = (new FindCustomerBySalepersonIdMobileQuery(null, $filters))->handle();
                }
            } else {
                $leadLists = (new FindCustomerBySalepersonIdMobileQuery($loginUser->id, $filters))->handle();
            }

            return response()->success($leadLists, 'success', Response::HTTP_OK);

        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function customerDetail(int $id)
    {
        //check if user's has permission
        abort_if(authorize('view', CustomerPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for User!');

        try {
            return response()->success((new FindCustomerByIdMobileQuery($id))->handle(), "Customer Detail", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {

            //check if user's has permission
            // abort_if(authorize('store', LeadPolicy::class), Response::HTTP_FORBIDDEN, 'Need store permission for User!');

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

                if ($request->generatePassword == "true") {

                    $randomString = Str::random(8);

                    $password = new Password($randomString, $randomString);
                } else {
                    $password = null;
                }

                $user = UserMapper::fromRequest($request);

                $roleIds = isset($request->role_ids) ? $request->role_ids : ['5'];

                $userData = (new StoreUserCommandMobile($user, $password, $roleIds, $salespersonIds))->execute();

                $customerName = $request->first_name . ' ' . $request->last_name;

                $siteSetting = SiteSettingEloquentModel::first();

                if ($request->generatePassword == "true") {
                    // Use "$randomString" instead of the old "$password->value" in an attempt to solve the issue where email contains encrypted password, instead of actual password
                    (new SendSuccessCreateLeadMailToCustomerCommand($customerName, $request->email, $randomString, $siteSetting, $salespersonNames))->execute();
                }

                $customer = CustomerMapper::fromRequest($request,null,$userData->id);

                $customerData = (new StoreCustomerCommandMobile($customer, $salespersonIds))->execute();

                foreach ($properties as $value) {
                    $propertyType = PropertyTypeEloquentModel::query()->firstOrCreate(
                        ['id' => $value->type],
                        ['type' => $value->type]
                    );

                    $property = PropertyMapper::fromRequest($value, null, $propertyType->id);

                    $propertyData = (new StorePropertyCommandMobile($property))->execute();

                    $customerData->customer_properties()->attach($propertyData->id);
                }

                $customerData->properties = $customerData->customer_properties;

                return response()->success($userData, "Lead Create Successful !", Response::HTTP_CREATED,);

            } catch (\DomainException $domainException) {
                return response()->json(['error' => $domainException->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
            } catch (UnauthorizedUserException $e) {
                return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
            }

        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function update(int $id, Request $request)
    {
        //check if user's has permission
        abort_if(authorize('update', LeadPolicy::class), Response::HTTP_FORBIDDEN, 'Need update permission for User!');

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

            if ($request->generatePassword == "true") {

                $randomString = Str::random(8);

                $password = new Password($randomString, $randomString);
            } else {
                $password = null;
            }

            $user = (new UpdateCustomerUserCommandMobile($id, $request->all(), $password))->execute();

            $customerName = $request->first_name . ' ' . $request->last_name;

            $siteSetting = SiteSettingEloquentModel::first();

            if ($request->generatePassword == "true") {
                // Use "$randomString" instead of the old "$password->value" in an attempt to solve the issue where email contains encrypted password, instead of actual password
                (new SendSuccessCreateLeadMailToCustomerCommand($customerName, $request->email, $randomString, $siteSetting, $salespersonNames))->execute();
            }

            $customerData = (new UpdateCustomerCommandMobile($request->all()))->execute();
            foreach ($properties as $value) {

                $propertyType = PropertyTypeEloquentModel::query()->firstOrCreate(
                    ['id' => $value->type],
                    ['type' => $value->type]
                );

                $id = $value->property_id == "" ? null : $value->property_id;

                $property = PropertyMapper::fromRequest($value, $id, $propertyType->id);

                $propertyData = (new StorePropertyCommandMobile($property))->execute();

                if ($value->property_id == "") {
                    $customerData->customer_properties()->attach($propertyData->id);
                }
            }

            return response()->success($user, "Lead Update Successful !", Response::HTTP_OK);

        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function inactiveCustomer(int $id, Request $request)
    {
        //check if user's has permission
        abort_if(authorize('change_customer_status', CustomerPolicy::class), Response::HTTP_FORBIDDEN, 'Need change_customer_status permission for User!');

        try {

            (new InactiveCustomerMobileCommand($id))->execute();
            $user_id = $id;
            return response()->success($user_id, "Successfully Make Inactive Status", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {
            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function getLeadWithProperties(Request $request)
    {

        //check if user's has permission
        // abort_if(authorize('view', CustomerPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for User!');

        try {
            return response()->success((new FindCustomerListWithPropertiesMobileQuery())->handle(), "Customer Lists", Response::HTTP_OK);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
