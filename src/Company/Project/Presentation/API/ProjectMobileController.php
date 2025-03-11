<?php

namespace Src\Company\Project\Presentation\API;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Src\Company\Project\Application\Mappers\ProjectMapper;
use Src\Company\Project\Application\Mappers\PropertyMapper;
use Src\Company\Project\Application\Policies\ProjectPolicy;
use Src\Company\Project\Application\UseCases\Commands\FirstorCreatePropertyTypeMobileCommand;
use Src\Company\Project\Application\UseCases\Commands\SendProjectAssignMailToCustomerMobileCommand;
use Src\Company\Project\Application\UseCases\Commands\StoreProjectMobileCommand;
use Src\Company\Project\Application\UseCases\Commands\StoreSaleReportMobileCommand;
use Src\Company\Project\Application\UseCases\Commands\UpdateProjectMobileCommand;
use Src\Company\Project\Application\UseCases\Commands\UpdatePropertyMobileCommand;
use Src\Company\Project\Application\UseCases\Queries\FindProjectByIdMobileQuery;
use Src\Company\Project\Application\UseCases\Queries\FindProjectByIdQuery;
use Src\Company\Project\Application\UseCases\Queries\FindProjectDetailForHandoverMobileQuery;
use Src\Company\Project\Application\UseCases\Queries\FindProjectForManagementMobileQuery;
use Src\Company\Project\Application\UseCases\Queries\FindProjectListForTableViewMobileQuery;
use Src\Company\Project\Application\UseCases\Queries\FindProjectReportMobileQuery;
use Src\Company\Project\Infrastructure\EloquentModels\ProjectEloquentModel;
use Src\Company\System\Application\UseCases\Commands\IncreaseQuotationNoMobileCommand;
use Src\Company\UserManagement\Application\UseCases\Queries\FindUserByIdMobileQuery;

class ProjectMobileController extends Controller
{

    public function index(Request $request): JsonResponse
    {
        try {

            try {

                $filters = $request->all();

                $projectLists = (new FindProjectListForTableViewMobileQuery($filters))->handle();

                return response()->success($projectLists, 'success', Response::HTTP_OK);
            } catch (UnauthorizedUserException $e) {

                return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
            }
        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $customerIds = json_decode($request->customer_id);
            $project = ProjectMapper::fromRequest($request);

            $projectLists = ProjectEloquentModel::with('properties')
            ->where(function ($query) {
                $query->where('project_status', 'InProgress')
                ->orWhere('project_status', 'New');
            })
                ->get();
    
            if ($request->postal_code || $request->unit_num) {
                if ($projectLists->contains(function ($project) use ($request) {
                    return $project->properties->postal_code == $request->postal_code && $project->properties->unit_num == $request->unit_num;
                })) {
                    return response()->error(['postal_code' => "Duplicate Property Address. Make Sure Unit Num & Postal Code are unique."], "Duplicate Property Address", 422);
                }
            }

            $projectData =  (new StoreProjectMobileCommand($project, $request))->execute();
            $projectId = $projectData->id;
            (new StoreSaleReportMobileCommand($projectId))->execute(); //store sale report with proejct id

            if (is_array($customerIds)) {
                foreach ($customerIds as $customerId) {
                    $user = (new FindUserByIdMobileQuery((int)$customerId->id))->handle();

                    $customerName = $user->first_name . ' ' . $user->last_name;
                    $customerEmail = $user->email;
                    $customerPassword = $user->password;

                    if (isset($customerPassword)) {
                        (new SendProjectAssignMailToCustomerMobileCommand($projectId, $customerName, $customerEmail, $customerPassword))->execute();
                    }
                }
            } else {
                $user = (new FindUserByIdMobileQuery($request->customer_id))->handle();

                $customerName = $user->first_name . $user->last_name;

                $customerEmail = $user->email;

                $customerPassword  = $user->password;

                if (isset($customerPassword)) {
                    (new SendProjectAssignMailToCustomerMobileCommand($projectId, $customerName, $customerEmail, $customerPassword))->execute();
                }
            }

            // increase project no
            (new IncreaseQuotationNoMobileCommand($request->company_id))->execute();

            DB::commit();

            return response()->success($projectData, 'Project Create Successful !', Response::HTTP_OK);

        } catch (Exception $ex) {
            DB::rollBack();
            return response()->error($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(int $id): JsonResponse
    {
        //check if user's has permission
        abort_if(authorize('view', ProjectPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Project!');

        try {

            $project = (new FindProjectByIdMobileQuery($id))->handle();
            return response()->success($project, 'success', Response::HTTP_OK);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function projectDetailForHandover($id){
        try {

            $projectData = (new FindProjectDetailForHandoverMobileQuery($id))->handle();
            return response()->success($projectData, 'success', Response::HTTP_OK);

        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function getProjectReport($id)
    {
        try {

            $data = (new FindProjectReportMobileQuery($id))->handle();

            return response()->success($data, 'success', Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function update($id, Request $request)
    {
        DB::beginTransaction();
        try {

            $project = ProjectMapper::fromRequest($request, $id);
            $projectLists = ProjectEloquentModel::with('properties')
                ->where('id', '!=', $id)
                ->where(function ($query) {
                    $query->where('project_status', 'InProgress')
                        ->orWhere('project_status', 'New');
                })
                ->get();

            if ($request->postal_code || $request->unit_num) {
                if ($projectLists->contains(function ($project) use ($request) {
                    return $project->properties->postal_code == $request->postal_code && $project->properties->unit_num == $request->unit_num;
                })) {
                    return response()->error(['postal_code' => "Duplicate Property Address. Make Sure Unit Num & Postal Code are unique."], "Duplicate Property Address", 422);
                }
            }

           
            $propertyType = (new FirstorCreatePropertyTypeMobileCommand($request->type))->execute();
            foreach ($request->customer_ids as $customer) {
                $property_id = $customer['property_id'] ? $customer['property_id'] : $request->property_id;
                $property = PropertyMapper::fromRequest($request, $property_id, $propertyType->id);
                (new UpdatePropertyMobileCommand($property))->execute();
            }

            $projectData = (new UpdateProjectMobileCommand($project, $request, $id))->execute();

            DB::commit();
            return response()->success($projectData, 'Project Update Successful !', Response::HTTP_OK);

        } catch (Exception $ex) {
            DB::rollBack();
            return response()->error($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getProjectForManagement(Request $request): JsonResponse
    {
        abort_if(authorize('view_by_management', ProjectPolicy::class), Response::HTTP_FORBIDDEN, 'Need view_by_management permission for Project!');

        try {

            $perPage = $request->perPage ?? 0;
            $salePerson = $request->salePerson ?? 0;
            $filterText = $request->filterText ?? '';
            $status = $request->status ?? '';
            $created_at = $request->created_at ?? '';
            $cardView = $request->cardView ?? false;

            $projects = (new FindProjectForManagementMobileQuery($perPage, $salePerson, $filterText, $status, $created_at, $cardView))->handle();

            return response()->success($projects, 'success', Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

}
