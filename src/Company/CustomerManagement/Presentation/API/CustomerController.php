<?php

namespace Src\Company\CustomerManagement\Presentation\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Symfony\Component\HttpFoundation\Response;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\CustomerManagement\Application\UseCases\Queries\FindCustomerListQuery;
use Src\Company\CustomerManagement\Application\Policies\CustomerPolicy;
use Src\Company\CustomerManagement\Application\Requests\CreateLeadRequest;
use Src\Company\CustomerManagement\Application\UseCases\Queries\FindCustomerByManagerIdQuery;
use Src\Company\CustomerManagement\Application\UseCases\Queries\FindCustomerBySalepersonIdQuery;
use Src\Company\CustomerManagement\Application\UseCases\Queries\GetCustomerListQuery;
use Src\Company\StaffManagement\Infrastructure\EloquentModels\StaffEloquentModel;
use Src\Company\CustomerManagement\Application\Requests\UpdateLeadRequest;
use Src\Company\CustomerManagement\Application\Mappers\CustomerMapper;
use Src\Company\CustomerManagement\Application\Requests\UpdateCheckListRequest;
use Src\Company\CustomerManagement\Application\UseCases\Commands\ActiveCustomerCommand;
use Src\Company\CustomerManagement\Application\UseCases\Commands\InactiveCustomerCommand;
use Src\Company\CustomerManagement\Application\UseCases\Commands\StoreCustomerCommand;
use Src\Company\CustomerManagement\Application\UseCases\Commands\UpdateCheckListStatusCommand;
use Src\Company\CustomerManagement\Application\UseCases\Commands\UpdateCustomerCommand;
use Src\Company\CustomerManagement\Application\UseCases\Commands\UpdateIdMilestoneCommand;
use Src\Company\CustomerManagement\Application\UseCases\Queries\CustomersWithEmailQuery;
use Src\Company\CustomerManagement\Application\UseCases\Queries\FindCustomerByIdQuery;
use Src\Company\CustomerManagement\Application\UseCases\Queries\FindIdMilestoneByUserIdQuery;
use Src\Company\CustomerManagement\Application\UseCases\Queries\FindIdMilestonesQuery;
use Src\Company\CustomerManagement\Application\UseCases\Queries\GetGroupSalepersonLeadManagementListQuery;
use Src\Company\CustomerManagement\Application\UseCases\Queries\GetManagerLeadManagementListQuery;
use Src\Company\CustomerManagement\Application\UseCases\Queries\GetSalepersonLeadManagementListQuery;
use Src\Company\CustomerManagement\Application\UseCases\Queries\LeadManagementReportQuery;
use Src\Company\Project\Application\Mappers\PropertyMapper;
use Src\Company\Project\Application\UseCases\Commands\StorePropertyCommand;
use Src\Company\Project\Infrastructure\EloquentModels\PropertyTypeEloquentModel;
use Src\Company\System\Application\UseCases\Queries\CampaignListQuery;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Maatwebsite\Excel\Facades\Excel;
use Src\Company\CustomerManagement\Domain\Imports\LeadExcelImport;

class CustomerController extends Controller
{
    public function getCustomers(Request $request): JsonResponse
    {
        abort_if(authorize('view', CustomerPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for User!');

        try {

            $filters = $request->all();

            return response()->success((new GetCustomerListQuery($filters))->handle(), "Customer Lists", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function getCustomerList(Request $request)
    {

        //check if user's has permission
        abort_if(authorize('view', CustomerPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for User!');

        try {

            $filters = $request->all();

            return response()->success((new FindCustomerListQuery($filters))->handle(), "Customer Lists", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function getCampaignList(Request $request)
    {
        abort_if(authorize('view', CustomerPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for User!');

        try {

            $filters = $request->all();
            return response()->success((new CampaignListQuery($filters))->handle(), "Campaing Lists", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }
    // Section Customer CRUD Functions

    public function customerList(Request $request)
    {
        //check if user's has permission
        abort_if(authorize('view', CustomerPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for User!');

        try {

            $filters = $request->all();

            $saleperson = auth('sanctum')->user();

            if ($saleperson->roles->contains('name', 'Salesperson')) {
                $staff_info = StaffEloquentModel::query()->where('user_id', $saleperson->id)->first();

                if (!$staff_info) {
                    return response()->error(['sale_rank' => "Cannot show lead list because require saleperson info (Rank) doesn't existed."], "Saleperson Info Not Sufficient", 400);
                }
            }

            $result = "";

            if ($saleperson->roles->contains('name', 'Management') || $saleperson->roles->contains('name', 'Marketing')) {
                if (isset($filters['saleperson_id'])) {
                    $result = (new FindCustomerBySalepersonIdQuery($filters['saleperson_id'], $filters))->handle();
                } else {
                    $result = (new FindCustomerBySalepersonIdQuery(null, $filters))->handle();
                }
            } else {
                $result = (new FindCustomerBySalepersonIdQuery($saleperson->id, $filters))->handle();
            }

            return response()->success($result, "Customer Lists", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function customerListForManager(Request $request)
    {
        //check if user's has permission
        abort_if(authorize('view', CustomerPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for User!');

        try {

            $filters = $request->all();

            $saleperson = auth('sanctum')->user();

            $result = "";

            $result = (new FindCustomerByManagerIdQuery($saleperson->id, $filters))->handle();

            return response()->success($result, "Customer Lists", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function createLead(Request $request)
    {
        //check if user's has permission
        abort_if(authorize('create_lead', CustomerPolicy::class), Response::HTTP_FORBIDDEN, 'Need store permission for User!');

        try {

            $properties = json_decode($request->properties);

            $salespersonIds = json_decode($request->saleperson_ids);

            $customer = CustomerMapper::fromRequest($request,null,$request->user_id);

            $customerData = (new StoreCustomerCommand($customer, $salespersonIds))->execute();

            foreach ($properties as $value) {
                $propertyType = PropertyTypeEloquentModel::query()->firstOrCreate(
                    ['id' => $value->type],
                    ['type' => $value->type]
                );

                $property = PropertyMapper::fromRequest($value, null, $propertyType->id);

                $propertyData = (new StorePropertyCommand($property))->execute();

                $customerData->customer_properties()->attach($propertyData->id);
            }

            $customerData->properties = $customerData->customer_properties;

            return response()->success($customerData, "Success", Response::HTTP_CREATED,);

        } catch (\DomainException $domainException) {
            return response()->json(['error' => $domainException->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function updateLead(int $id, UpdateLeadRequest $request): JsonResponse
    {
        //check if user's has permission
        abort_if(authorize('update_lead', CustomerPolicy::class), Response::HTTP_FORBIDDEN, 'Need update permission for User!');

        try {

            $properties = json_decode($request->properties);


            $customerData = (new UpdateCustomerCommand($request->all()))->execute();

            foreach ($properties as $value) {

                $propertyType = PropertyTypeEloquentModel::query()->firstOrCreate(
                    ['id' => $value->type],
                    ['type' => $value->type]
                );

                $id = $value->property_id == "" ? null : $value->property_id;

                $property = PropertyMapper::fromRequest($value, $id, $propertyType->id);

                $propertyData = (new StorePropertyCommand($property))->execute();

                if ($value->property_id == "") {
                    $customerData->customer_properties()->attach($propertyData->id);
                }
            }
            $customerData->properties = $customerData->customer_properties;

            return response()->success($customerData, "Success", Response::HTTP_OK);

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

            (new InactiveCustomerCommand($id))->execute();
            $user_id = $id;
            return response()->success($user_id, "Successfully Make Inactive Status", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {
            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function activeCustomer(int $id)
    {
        //check if user's has permission
        abort_if(authorize('change_customer_status', CustomerPolicy::class), Response::HTTP_FORBIDDEN, 'Need change_customer_status permission for User!');

        try {
            (new ActiveCustomerCommand($id))->execute();

            $user_id = $id;

            return response()->success($user_id, "Successfully Make Active Status", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function customerDetail(int $id)
    {
        //check if user's has permission
        abort_if(authorize('view', CustomerPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for User!');

        try {
            return response()->success((new FindCustomerByIdQuery($id))->handle(), "Customer Detail", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function customersWithEmail()
    {
        abort_if(authorize('view', CustomerPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for User!');

        try {

            return response()->success((new CustomersWithEmailQuery())->handle(), "Customer Lists", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }


    public function salepersonLeadManagementReport(Request $request)
    {
        try {
            return response()->success((new LeadManagementReportQuery($request->all()))->handle(), "Lead Management Report", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function salepersonLeadManagementList(Request $request)
    {
        //check if user's has permission
        abort_if(authorize('view', CustomerPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for User!');

        try {

            $filters = $request->all();
            $saleperson_id = $request->saleperson_id;

            $saleperson = auth('sanctum')->user();

            if ($saleperson->roles->contains('name', 'Salesperson')) {
                $staff_info = StaffEloquentModel::query()->where('user_id', $saleperson->id)->first();

                if (!$staff_info) {
                    return response()->error(['sale_rank' => "Cannot show lead list because require saleperson info (Rank) doesn't existed."], "Saleperson Info Not Sufficient", 400);
                }
            }

            $result = (new GetSalepersonLeadManagementListQuery($saleperson_id, $filters))->handle();
            return $result;
            return response()->success($result, "Customer Lists", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function groupSalepersonLeadManagementList(Request $request)
    {
        //check if user's has permission
        abort_if(authorize('view', CustomerPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for User!');

        try {

            $filters = $request->all();
            $mgr_id = $request->mgr_id;

            $saleperson = auth('sanctum')->user();

            if ($saleperson->roles->contains('name', 'Salesperson')) {
                $staff_info = StaffEloquentModel::query()->where('user_id', $saleperson->id)->first();

                if (!$staff_info) {
                    return response()->error(['sale_rank' => "Cannot show lead list because require saleperson info (Rank) doesn't existed."], "Saleperson Info Not Sufficient", 400);
                }
            }

            $result = (new GetGroupSalepersonLeadManagementListQuery($mgr_id, $filters))->handle();
            return $result;
            return response()->success($result, "Customer Lists", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function managerLeadManagementList(Request $request)
    {
        //check if user's has permission
        abort_if(authorize('view', CustomerPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for User!');

        try {

            $filters = $request->all();
            $manager_id = $request->manager_id;

            $result = (new GetManagerLeadManagementListQuery($manager_id, $filters))->handle();
            return $result;
            return response()->success($result, "Customer Lists", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function changeIdMilestones(Request $request)
    {
        try {

            $data = $request->all();

            $result = (new UpdateIdMilestoneCommand($data))->execute();

            return response()->success($result, "ID Milestone Change Success", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function updateCheckListStatus(UpdateCheckListRequest $request)
    {
        try {

            $data = $request->all();

            (new UpdateCheckListStatusCommand($data))->execute();

            return response()->success($data, "Successfully Updated Check List Status", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function customerExcelImport(Request $request)
    {
        try {

            $uploadFile = $request->file('lead_excel_file');

            // Convert spreadsheet to array
            $sheetsData = Excel::toArray([], $uploadFile);

            foreach ($sheetsData as $sheet) {
                $import = new LeadExcelImport();
                $import->collection(collect($sheet));
            }
            return response()->success(null, "Successfully Lead Excel Imported", Response::HTTP_OK);

        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }
}
