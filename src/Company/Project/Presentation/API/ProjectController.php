<?php

namespace Src\Company\Project\Presentation\API;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Src\Company\System\Domain\Mail\TestMail;
use Symfony\Component\HttpFoundation\Response;
use Src\Company\System\Domain\Mail\TestCssEmail;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\Project\Application\Mappers\ProjectMapper;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Src\Company\Project\Application\Mappers\PropertyMapper;
use Src\Company\Project\Application\Policies\ProjectPolicy;
use Src\Company\Document\Application\Mappers\DocumentMapper;
use Src\Company\Document\Domain\Export\ProfitAndLossExport;
use Src\Company\Project\Application\Requests\StoreProjectRequest;
use Src\Company\Project\Domain\Repositories\ProjectRepositoryInterface;
use Src\Company\Project\Application\UseCases\Commands\CancelProjectById;
use Src\Company\Project\Application\UseCases\Commands\StoreProjectCommand;
use Src\Company\Project\Application\UseCases\Queries\FindAllProjectsQuery;
use Src\Company\Project\Application\UseCases\Queries\FindProjectByIdQuery;
use Src\Company\Project\Application\UseCases\Queries\FindOngoingProjectsQuery;
use Src\Company\UserManagement\Application\UseCases\Queries\FindUserByIdQuery;
use Src\Company\Project\Application\UseCases\Commands\StorePropertyCommand;
use Src\Company\Project\Infrastructure\EloquentModels\ProjectEloquentModel;
use Src\Company\Project\Application\UseCases\Commands\UpdatePropertyCommand;
use Src\Company\Project\Infrastructure\EloquentModels\PropertyTypeEloquentModel;
use Src\Company\Project\Application\UseCases\Commands\SendProjectAssignMailToCustomerCommand;
use Src\Company\Project\Application\UseCases\Queries\FindProjectByCustomerIdQuery;
use Src\Company\Project\Application\UseCases\Queries\FindProjectDetailForHandoverQuery;
use Src\Company\Project\Infrastructure\EloquentModels\PropertyEloquentModel;
use Src\Company\System\Infrastructure\EloquentModels\CompanyEloquentModel;
use Src\Company\Project\Application\UseCases\Commands\DeleteProjectCommand;
use Src\Company\Project\Application\UseCases\Commands\UpdateProjectCommand;
use Src\Company\Project\Application\UseCases\Commands\StoreSaleReportCommand;
use Src\Company\Project\Application\UseCases\Commands\ToggleFreezedProjectById;
use Src\Company\Project\Application\UseCases\Commands\RetrieveProjectByIdCommand;
use Src\Company\Project\Application\UseCases\Queries\FindProjectForManagementQuery;
use Src\Company\Project\Application\UseCases\Queries\FindProjectListForTableViewQuery;
use Src\Company\Project\Application\UseCases\Commands\FirstorCreatePropertyTypeCommand;
use Src\Company\Project\Application\UseCases\Commands\PendingCancelProjectCommand;
use Src\Company\Project\Application\UseCases\Commands\UpdateEstimatedDateCommand;
use Src\Company\Project\Application\UseCases\Queries\FindProjectListsForCustomerAndOthersQuery;
use Src\Company\Project\Application\UseCases\Queries\GetNewProjectListQuery;
use Src\Company\Project\Application\UseCases\Queries\GetPendingCancelProjectsQuery;
use Src\Company\Project\Infrastructure\EloquentModels\ContactUserEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\SaleReportEloquentModel;
use Src\Company\System\Application\UseCases\Commands\IncreaseProjectNoCommand;
use Src\Company\System\Domain\Model\Entities\GeneralSetting;
use Src\Company\System\Application\UseCases\Commands\IncreaseQuotationNoCommand;
use Src\Company\System\Infrastructure\EloquentModels\GeneralSettingEloquentModel;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;

class ProjectController extends Controller
{
    private $projectInterface;

    public function __construct(ProjectRepositoryInterface $projectRepository)
    {
        $this->projectInterface = $projectRepository;
    }

    // Saleperson Project List Card View Api
    public function index(): JsonResponse
    {
        //check if user's has permission
        abort_if(authorize('view', ProjectPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Project!');


        try {
            $projects = $this->projectInterface->getProjects();

            return response()->success($projects, 'success', Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    // Saleperson Project List Table View Api
    public function listView(Request $request): JsonResponse
    {
        //check if user's has permission
        abort_if(authorize('view', ProjectPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Project!');

        try {

            // return response()->success($request->all(), 'success', Response::HTTP_OK);

            $filters = $request->all();

            $projects = (new FindProjectListForTableViewQuery($filters))->handle();

            return response()->success($projects, 'success', Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    // Customer & others Roles Project List Query View Api
    public function projectListsForOthers(Request $request)
    {
        //check if user's has permission
        abort_if(authorize('view', ProjectPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Project!');

        try {

            // return response()->success($request->all(), 'success', Response::HTTP_OK);

            $filters = $request->all();

            $projects = (new FindProjectListsForCustomerAndOthersQuery($filters))->handle();

            return response()->success($projects, 'success', Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function projectList(): JsonResponse
    {
        //check if user's has permission
        abort_if(authorize('view', ProjectPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Project!');

        try {
            $projects = $this->projectInterface->getProjectLists();

            return response()->success($projects, 'success', Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function customerProject($customerId)
    {
        abort_if(authorize('view', ProjectPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Project!');

        try {

            $project = (new FindProjectByCustomerIdQuery($customerId))->handle();
            return response()->success($project, 'success', Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function show(int $id): JsonResponse
    {
        //check if user's has permission
        abort_if(authorize('view', ProjectPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Project!');

        try {

            $project = (new FindProjectByIdQuery($id))->handle();
            return response()->success($project, 'success', Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function store(StoreProjectRequest $request)
    {
        //check if user's has permission
        abort_if(authorize('store', ProjectPolicy::class), Response::HTTP_FORBIDDEN, 'Need store permission for Project!');
        DB::beginTransaction();
        try {

            $projectLists = ProjectEloquentModel::with('properties')
                ->where(function ($query) {
                    $query->where('project_status', 'InProgress')
                        ->orWhere('project_status', 'New');
                })
                ->get();

            // if($request->lead_project_count > 0 && !$request->postal_code && !$request->unit_num)
            // {
            //     return response()->error(null,"Current lead can't be create new project!",422);
            // }

            if ($request->postal_code || $request->unit_num) {
                if ($projectLists->contains(function ($project) use ($request) {
                    return $project->properties->postal_code == $request->postal_code && $project->properties->unit_num == $request->unit_num;
                })) {
                    return response()->error(['postal_code' => "Duplicate Property Address. Make Sure Unit Num & Postal Code are unique."], "Duplicate Property Address", 422);
                }
            }

            // $createdDate = date("my", strtotime(Carbon::now()));

            PropertyEloquentModel::where('id', $request->property_id)->update([
                'street_name' => $request->street_name,
                'block_num' => $request->block_num,
                'unit_num' => $request->unit_num,
                'postal_code' => $request->postal_code,
                'type_id' => $request->type
            ]);

            $customerIds = json_decode($request->customer_id);

            $projectEloquent = ProjectEloquentModel::create([
                'description' => $request->description,
                'project_status' => 'New',
                'company_id' => $request->company_id,
                // 'customer_id' => $request->customer_id,
                'customer_id' => $customerIds[0]->id,
                // 'agreement_no' => $agreementNum,
                'property_id' => $request->property_id,
                'created_by' => auth('sanctum')->user()->id,
                'term_and_condition_id' => $request->term_and_condition_id
            ]);

            if ($request->user_id && $request->user_id != 'null') {
                $projectEloquent->contactUser()->create([
                    'user_id' => $request->user_id,
                ]);
            }

            $projectId = $projectEloquent->id;

            $companyName = CompanyEloquentModel::findOrFail($request->company_id, ['docu_prefix','quotation_no','quotation_prefix']);

            $initialCompanyName = $companyName->docu_prefix;
            $initialProjectNum = $companyName->quotation_no;
            $quotationPrefix = $companyName->quotation_prefix;

            $name = auth('sanctum')->user()->first_name . ' ' . auth('sanctum')->user()->last_name;
            $words = array_filter(explode(" ", $name));
            $initials = '';

            foreach ($words as $word) {
                if (strlen($word) > 0) {
                    $initials .= $word[0];
                }
            }
            $common_quotation_running_number = 0;
            $common_project_running_number = 0;
            $checkCommonQuotationNumSetting = GeneralSettingEloquentModel::where('setting', "enable_common_quotation_running_number")
                ->where('value', "true")
                ->first();
            $checkCommonProjectNumSetting = GeneralSettingEloquentModel::where('setting', "enable_common_project_running_number")
                ->where('value', "true")
                ->first();
            $enableDataMigrationMode = GeneralSettingEloquentModel::where('setting', "enable_data_migration_mode")
                ->where('value', "true")
                ->first();

            if($checkCommonProjectNumSetting){
                $commonPjNum = GeneralSettingEloquentModel::where('setting','common_project_start_number')->first();
                // $runningNum = $commonPjNum->value;
                $common_project_running_number = $commonPjNum->value;
            }

            if($checkCommonQuotationNumSetting){
                $commonQONum = GeneralSettingEloquentModel::where('setting','common_quotation_start_number')->first();
                // $runningNum = $commonPjNum->value;
                $common_quotation_running_number = $commonQONum->value;
            }

            $initialSalespersonName = $initials;
            $block_num = $request->input('block_num');
            $block_num = str_replace(array('Blk', 'Blk ', 'Block', 'Block ','blk','blk ','BLK','BLK ','BLOCK','BLOCK '), '', $block_num);
            if(!$enableDataMigrationMode){
            $agreementNum = generateAgreementNumber('project',[
                'company_initial' => $initialCompanyName,
                'quotation_initial' => $quotationPrefix,
                'salesperson_initial' => $initialSalespersonName,
                'block_num' => $block_num,
                'date' => Carbon::now()->toDateString(),
                'project_id' => $projectId, 
                'quotation_num' => $initialProjectNum ?? null,
                'running_num' => $runningNum ?? null,
                'project_agr_no' => null,
                'common_project_running_number' => $common_project_running_number,
                'common_quotation_running_number' => $common_quotation_running_number
            ]);
            // $agreement_no = "$initialCompanyName/$initialSalespersonName/$createdDate/$block_num";

            $projectEloquent->agreement_no = $agreementNum;
            }
            $projectEloquent->save();

            foreach ($customerIds as $customer) {
                $projectEloquent->customersPivot()->attach($customer->id, ['property_id' => $customer->property_id]);
            }
            $salespersonIds = json_decode($request->salesperson_ids);

            foreach ($salespersonIds as $id) {
                $projectEloquent->salespersons()->attach($id);
            }

            // $project = ProjectMapper::fromRequest($request);

            // $property = PropertyMapper::fromRequest($request, null, $request->type);

            // $salespersonIds = json_decode($request->salesperson_ids);
            // // $salespersonIds = $request->salesperson_ids;

            // $propertyData = (new StorePropertyCommand($property))->execute();

            // $projectData = (new StoreProjectCommand($project, $propertyData->id, $salespersonIds, null, $propertyData->block_num))->execute();


            //for attachment
            // $document = DocumentMapper::fromRequest($request);

            // if($request->attachment_file){
            //     $projectData = (new StoreProjectCommand($project, $propertyData->id, $salespersonIds, $document, $propertyData->block_num))->execute();
            // } else {
            //     $projectData = (new StoreProjectCommand($project, $propertyData->id, $salespersonIds, null, $propertyData->block_num))->execute();
            // }
            //end

            // $projectId = $projectData->id;

            (new StoreSaleReportCommand($projectId))->execute(); //store sale report with proejct id


            // if (is_array($customerIds)) {
            //     foreach ($customerIds as $customerId) {
            //         $user = (new FindUserByIdQuery((int)$customerId->id))->handle();

            //         $customerName = $user->first_name . ' ' . $user->last_name;
            //         $customerEmail = $user->email;
            //         $customerPassword = $user->password;

            //         if (isset($customerPassword)) {
            //             (new SendProjectAssignMailToCustomerCommand($projectId, $customerName, $customerEmail, $customerPassword))->execute();
            //         }
            //     }
            // } else {
            //     $user = (new FindUserByIdQuery($request->customer_id))->handle();

            //     $customerName = $user->first_name . $user->last_name;

            //     $customerEmail = $user->email;

            //     $customerPassword  = $user->password;

            //     if (isset($customerPassword)) {
            //         (new SendProjectAssignMailToCustomerCommand($projectId, $customerName, $customerEmail, $customerPassword))->execute();
            //     }
            // }

            // increase project no
            if(!$enableDataMigrationMode){
            (new IncreaseQuotationNoCommand($request->company_id))->execute();
            }
            DB::commit();
            // projectData
            return response()->json($projectEloquent, Response::HTTP_CREATED);
        } catch (\DomainException $domainException) {

            return response()->json(['error' => $domainException->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function update(int $id, StoreProjectRequest $request): JsonResponse
    {
        //check if user's has permission
        abort_if(authorize('update', ProjectPolicy::class), Response::HTTP_FORBIDDEN, 'Need update permission for Project!');

        try {

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

            $project = ProjectMapper::fromRequest($request, $id);

            $propertyType = (new FirstorCreatePropertyTypeCommand($request->type))->execute();
            foreach ($request->customer_ids as $customer) {
                $property_id = $customer['property_id'] ? $customer['property_id'] : $request->property_id;
                $property = PropertyMapper::fromRequest($request, $property_id, $propertyType->id);
                (new UpdatePropertyCommand($property))->execute();
            }


            $salespersonIds = $request->salesperson_ids;
            $customerIds = $request->customer_ids;
            if ($request->user_id && $request->user_id != 'null') {
                ContactUserEloquentModel::updateOrCreate(
                    ['project_id' => $project->id],
                    ['user_id' => $request->user_id]
                );
            }else{
                $contact_user = ContactUserEloquentModel::where('project_id', $project->id)->first();
                if($contact_user){
                    $contact_user->delete();
                }
            }


            (new UpdateProjectCommand($project, $salespersonIds, $request->agreement_no, $customerIds, $id))->execute();

            return response()->json($project, Response::HTTP_OK);
        } catch (\DomainException $domainException) {

            return response()->json(['error' => $domainException->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        //check if user's has permission
        abort_if(authorize('destroy', ProjectPolicy::class), Response::HTTP_FORBIDDEN, 'Need destroy permission for Project!');

        try {
            (new DeleteProjectCommand($id))->execute();
            $data = [
                'id' => $id,
                'message' => 'success'
            ];
            return response()->json($data, Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function countProject(): JsonResponse
    {
        //check if user's has permission
        abort_if(authorize('view', ProjectPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Project!');

        try {
            $countProjects = ProjectEloquentModel::query()->count();
            $countProjects = Str::padLeft($countProjects + 1, 5, "0");
            return response()->success($countProjects, "count project", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function projectDetailForHandover($projectId)
    {
        abort_if(authorize('view', ProjectPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Project!');

        try {

            $projectData = (new FindProjectDetailForHandoverQuery($projectId))->handle();

            return response()->success($projectData, "Project Detail", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function onGoingProjectLists()
    {
        abort_if(authorize('view_ongoing_project', ProjectPolicy::class), Response::HTTP_FORBIDDEN, 'Need view_ongoing_project permission for Project!');

        try {

            $ongoingProjects = (new FindOngoingProjectsQuery())->handle();

            return response()->success($ongoingProjects, "Ongoing Projects ( limit 3 and order by desc )", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function getProjectForManagement(Request $request): JsonResponse
    {
        abort_if(authorize('view_by_management', ProjectPolicy::class), Response::HTTP_FORBIDDEN, 'Need view_by_management permission for Project!');

        try {

            $perPage = $request->perPage;
            $salePerson = $request->salePerson ?? 0;
            $filterText = $request->filterText ?? '';
            $status = $request->status ?? '';
            $created_at = $request->created_at ?? '';

            $projects = (new FindProjectForManagementQuery($perPage, $salePerson, $filterText, $status, $created_at))->handle();

            return response()->success($projects, 'success', Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    function cancelProject($projectId)
    {
        try {
            $projects = (new CancelProjectById($projectId))->execute();

            return response()->success($projects, 'success', Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    function retrieveProject($projectId)
    {
        try {
            $projects = (new RetrieveProjectByIdCommand($projectId))->execute();

            return response()->success($projects, 'success', Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    function toggleFreezeProject($projectId)
    {
        try {
            $projects = (new ToggleFreezedProjectById($projectId))->execute();

            return response()->success($projects, 'success', Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }    

    function UpdateEstimatedDate(Request $request)
    {
        $customer_payments = $request->customer_payments;
        try {
            $updated_customer_payments = (new UpdateEstimatedDateCommand($customer_payments))->execute();

            return response()->success($updated_customer_payments, 'success', Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }    

    // function sendCustomerEmail(Request $request)
    // {

    //     // return $emailContent . $trackingPixelHtml;
    //     $toEmail = $request->customer_email;

    //     if(isset($request->email_content))
    //     {
    //         $htmlContent = $request->email_content;
    //     } else {
    //         $htmlContent = '<h1>Mail Testing</h1>';
    //     }

    //     // $pattern = '/<img src="data:image\/([^;]+);base64,([^"]+)"/i';

    //     // $newContent = preg_replace_callback($pattern, function ($matches) {
    //     //     $newImgTag = '<img class="processed-image" src="data:image/' . $matches[1] . ';base64,' . $matches[2] . '"';

    //     //     return $newImgTag;
    //     // }, $htmlContent);

    //     $secretCode = rand(100000, 999999);
    //     $trackingUrl = url("/api/email/track/$secretCode");
    //     $trackingPixelHtml = '<img src="' . $trackingUrl . '" width="1" height="1" style="display:none" alt=""/>';

    //     $email_content = $htmlContent . $trackingPixelHtml;
    //     $emailRecord = DB::table('email_records');
    //     $emailRecord->insert([
    //         'secret' => $secretCode,
    //         'email' => $toEmail
    //     ]);
    //     try {
    //         Mail::to($toEmail)->send(new TestMail($email_content));
    //         // Mail::to($toEmail)->send(new TestCssEmail($email_content));
    //         return response()->success('Success', 'success', Response::HTTP_OK);
    //     } catch (\Exception $e) {
    //     }
    // }

    // function trackEmailBySecret($secret)
    // {
    //     $emailRecord = DB::table('email_records')->where('secret', $secret)->update([
    //         'is_open' => 1
    //     ]);
    // }

    // function emailImageUpload(Request $request)
    // {
    //     $fileName =  time() . '.' . $request->image->extension();

    //     $filePath = 'campaign_email_images/' . $fileName;

    //     Storage::disk('public')->put($filePath, file_get_contents($request->image));

    //     $returnUrl = asset('storage/campaign_email_images/' . $fileName);

    //     return response()->success($returnUrl, 'success', Response::HTTP_OK);

    // }

    public function getNewProjectList(Request $request): JsonResponse
    {
        try {

            $perPage = $request->perPage;
            $salePerson = $request->salePerson ?? 0;
            $companyId = $request->companyId ?? 0;
            $filterText = $request->filterText ?? '';
            $status = $request->status ?? '';

            $filters = $request->all();

            if (!isset($filters['cardView'])) {
                $filters['cardView'] = 'false';
            }

            $projects = (new GetNewProjectListQuery($perPage, $salePerson,$companyId,$filterText,$status, $filters))->handle();

            return response()->success($projects, 'success', Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function exportProfitAndLoss($projectId)
    {
        return Excel::download(new ProfitAndLossExport($this->projectInterface, $projectId), 'ProjectReport.xlsx');
    }

    public function getPendingCancelProjects(Request $request): JsonResponse
    {
        try {

            $perPage = $request->perPage;

            $projects = (new GetPendingCancelProjectsQuery($perPage))->handle();

            return response()->success($projects, 'success', Response::HTTP_OK);

        } catch (UnauthorizedUserException $e) {

            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function pendingCancelProject(int $id)
    {
        try {
            $project = (new PendingCancelProjectCommand($id))->execute();

            return response()->success($project, 'success', Response::HTTP_OK);
        } catch (Exception $e) {

            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }
}
