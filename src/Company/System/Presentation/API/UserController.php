<?php

namespace Src\Company\System\Presentation\API;

use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\UnauthorizedException;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\CustomerManagement\Application\Requests\UpdateLeadRequest;
use Src\Company\CustomerManagement\Application\Requests\UpdateCheckListRequest;
use Src\Company\CustomerManagement\Application\UseCases\Commands\InactiveCustomerCommand;
use Src\Company\CustomerManagement\Application\UseCases\Commands\ActiveCustomerCommand;
use Src\Company\CustomerManagement\Application\UseCases\Commands\UpdateCheckListStatusCommand;
use Src\Company\CustomerManagement\Application\UseCases\Queries\FindCustomerByIdQuery;
use Src\Company\CustomerManagement\Application\UseCases\Queries\FindCustomerBySalepersonIdQuery;
use Src\Company\CustomerManagement\Application\UseCases\Queries\FindCustomerListQuery;
use Src\Company\System\Application\UseCases\Queries\FindSalepersonListQuery;
use Symfony\Component\HttpFoundation\Response;
use Src\Company\UserManagement\Domain\Model\ValueObjects\Password;;
use Src\Company\System\Domain\Repositories\UserRepositoryInterface;
use Src\Company\UserManagement\Application\Policies\UserPolicy;
use Src\Company\System\Application\Policies\LeadPolicy;
use Src\Company\System\Application\UseCases\Commands\AssignRankToSalepersonCommand;
use Src\Company\System\Application\UseCases\Queries\GetRankListQuery;
use Src\Company\CustomerManagement\Application\UseCases\Commands\SendSuccessCreateLeadMailToCustomerCommand;
use Src\Company\System\Application\UseCases\Commands\UpdateCustomerCommand;
use Src\Company\StaffManagement\Infrastructure\EloquentModels\StaffEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\PropertyEloquentModel;
use Src\Company\CustomerManagement\Application\UseCases\Queries\GetCustomerListQuery;
use Src\Company\Document\Domain\Export\UserExport;
use Src\Company\System\Application\Policies\CampaignManagementPolicy;
use Src\Company\System\Application\Requests\CampaignEmailRequest;
use Src\Company\CustomerManagement\Application\UseCases\Commands\UpdateIdMilestoneCommand;
use Src\Company\System\Application\UseCases\Queries\CampaignListQuery;
use Src\Company\CustomerManagement\Application\UseCases\Queries\CustomersWithEmailQuery;
use Src\Company\System\Application\UseCases\Queries\FindAllManagementOrManagerQuery;
use Src\Company\CustomerManagement\Application\UseCases\Queries\FindCustomerByManagerIdQuery;
use Src\Company\System\Application\UseCases\Queries\FindSalepersonReportListQuery;
use Src\Company\System\Application\UseCases\Queries\GetAllSalePersonForVendorInvoiceFilterQuery;
use Src\Company\CustomerManagement\Application\UseCases\Queries\GetGroupSalepersonLeadManagementListQuery;
use Src\Company\CustomerManagement\Application\UseCases\Queries\GetManagerLeadManagementListQuery;
use Src\Company\CustomerManagement\Application\UseCases\Queries\GetSalepersonLeadManagementListQuery;
use Src\Company\CustomerManagement\Application\UseCases\Queries\LeadManagementReportQuery;
use Src\Company\System\Domain\Imports\LeadExcelImport;
use Src\Company\System\Domain\Imports\StaffExcelImport;
use Src\Company\System\Infrastructure\EloquentModels\SiteSettingEloquentModel;
use Src\Company\System\Domain\Mail\CampaignMail;
use Src\Company\System\Infrastructure\EloquentModels\CampaignAudiencesEloquentModel;
use Src\Company\System\Infrastructure\EloquentModels\CampaignEloquentModel;

class UserController extends Controller
{
    private $userInterFace;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userInterFace = $userRepository;
    }




    public function getSalesPerson(): JsonResponse
    {
        try {

            $users = $this->userInterFace->getUsersByRole();

            return response()->success($users, 'success', Response::HTTP_OK);
        } catch (UnauthorizedException $e) {
            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function getCustomers(Request $request): JsonResponse
    {
        abort_if(authorize('view', UserPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for User!');

        try {

            $filters = $request->all();

            return response()->success((new GetCustomerListQuery($filters))->handle(), "User Lists", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function getSalepersonList(Request $request)
    {
        abort_if(authorize('view_salesperson', UserPolicy::class), Response::HTTP_FORBIDDEN, 'Need view_saleperson permission for User!');

        try {

            $filters = $request->all();

            return response()->success((new FindSalepersonListQuery($filters))->handle(), "Saleperson Lists", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function getDesignerListsForVendorFilter()
    {
        abort_if(authorize('view_salesperson', UserPolicy::class), Response::HTTP_FORBIDDEN, 'Need view_saleperson permission for Sale Person Lists!');

        try {
            $desingers = (new GetAllSalePersonForVendorInvoiceFilterQuery())->handle();

            return response()->success($desingers, "Desinger Lists For Vendor Invoice Filter", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function getSalepersonReportList(Request $request)
    {
        abort_if(authorize('view_salesperson', UserPolicy::class), Response::HTTP_FORBIDDEN, 'Need view_saleperson permission for User!');

        try {

            // $filters = $request->all();

            return response()->success((new FindSalepersonReportListQuery())->handle(), "Saleperson Lists", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }

    }

    public function getCustomerList(Request $request)
    {

        //check if user's has permission
        abort_if(authorize('view', LeadPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for User!');

        try {

            $filters = $request->all();

            return response()->success((new FindCustomerListQuery($filters))->handle(), "Customer Lists", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function getDrafterList()
    {
        try {

            $customers = $this->userInterFace->getDrafters();

            return response()->success($customers, 'Drafter Lists.', Response::HTTP_OK);
        } catch (UnauthorizedException $e) {
            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    // Section Customer CRUD Functions

    public function customerList(Request $request)
    {
        //check if user's has permission
        abort_if(authorize('view', LeadPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for User!');

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
                if(isset($filters['saleperson_id'])){
                    $result = (new FindCustomerBySalepersonIdQuery($filters['saleperson_id'], $filters))->handle();
                }else{
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
        abort_if(authorize('view', LeadPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for User!');

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

    public function updateUser(int $id, UpdateLeadRequest $request): JsonResponse
    {
        //check if user's has permission
        abort_if(authorize('update', LeadPolicy::class), Response::HTTP_FORBIDDEN, 'Need update permission for User!');

        try {

            $properties = json_decode($request->properties);

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

            // if(count($duplicateProperty) > 0){
            //     return response()->error($duplicateProperty,"Duplicate Property Address",422);
            // }

            // $propertyLists = PropertyEloquentModel::query()->where('id','!=',$request->property_id)->get();

            // if($request->postal_code || $request->unit_num){

            //     $data = $propertyLists->contains(function ($property) use ($request) {
            //         return $property->postal_code == $request->postal_code && $property->unit_num == $request->unit_num;
            //     });

            //     if ($data) {
            //         return response()->error(['postal_code' => "Duplicate Property Address. Make Sure Unit Num & Postal Code are unique."],"Duplicate Property Address",422);
            //     }
            // }

            $salespersonIds = json_decode($request->saleperson_ids);


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

            $user = (new UpdateCustomerCommand($id, $request->all(), $password))->execute();

            $customerName = $request->first_name . ' ' . $request->last_name;

            $siteSetting = SiteSettingEloquentModel::first();

            if ($request->create_account == "true") {
                // Use "$randomString" instead of the old "$password->value" in an attempt to solve the issue where email contains encrypted password, instead of actual password
                (new SendSuccessCreateLeadMailToCustomerCommand($customerName, $request->email, $randomString, $siteSetting, $salespersonNames))->execute();
            }

            $request->customer_id = $user->id;

            // $customerEloquent = CustomerEloquentModel::query()->where('user_id', $user->id)->first();

            // foreach ($properties as $value) {

            //     $propertyType = PropertyTypeEloquentModel::query()->firstOrCreate(
            //         ['id' => $value->type],
            //         ['type' => $value->type]
            //     );

            //     $id = $value->property_id == "" ? null : $value->property_id;

            //     $property = PropertyMapper::fromRequest($value, $id, $propertyType->id);

            //     $propertyData = (new StorePropertyCommand($property))->execute();

            //     if ($value->property_id == "") {
            //         $customerEloquent->customer_properties()->attach($propertyData->id);
            //     }
            // }

            return response()->success($user, "Success", Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function inactiveCustomer(int $id, Request $request)
    {
        //check if user's has permission
        abort_if(authorize('change_customer_status', LeadPolicy::class), Response::HTTP_FORBIDDEN, 'Need change_customer_status permission for User!');

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
        abort_if(authorize('change_customer_status', LeadPolicy::class), Response::HTTP_FORBIDDEN, 'Need change_customer_status permission for User!');

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
        abort_if(authorize('view', LeadPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for User!');

        try {
            return response()->success((new FindCustomerByIdQuery($id))->handle(), "Customer Detail", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function getRankList(): JsonResponse
    {
        // abort_if(authorize('view', UserPolicy::class), Response::HTTP_FORBIDDEN, '403 Forbidden');

        try {

            return response()->success((new GetRankListQuery())->handle(), "Rank Lists", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function assignSalepersonRank($salepersonId, $rankId)
    {
        try {

            (new AssignRankToSalepersonCommand($salepersonId, $rankId))->execute();
            return response()->success(null, "Successfully Updated Rank", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {
            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
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

    //For User Lists and Customer Lists in Both Superadmin and Management
    public function exportUsers(UserExport $userExport)
    {
        // Assuming you have a method to export or return the Excel file
        return Excel::download($userExport, 'users.xlsx');
    }

    //Sending Email Campaign to Customers
    public function sendCustomerEmail(CampaignEmailRequest $request)
    {

        abort_if(authorize('send', CampaignManagementPolicy::class), Response::HTTP_FORBIDDEN, 'Need Send permission for Campaign Mail!');

        $customerData = $request->customer_data;

        if (isset($request->email_content)) {
            $htmlContent = $request->email_content;
        } else {
            $htmlContent = '<h1>Mail Testing</h1>';
        }

        $campaign = CampaignEloquentModel::create([
            'title' => $request->campaign_title,
            'content' => $htmlContent
        ]);

        foreach ($customerData as $data) {

            $secretCode = rand(100000, 999999);
            $trackingUrl = url("/api/email/track/$secretCode");
            $trackingPixelHtml = '<img src="' . $trackingUrl . '" width="1" height="1" style="display:none" alt=""/>';

            $email_content = $htmlContent . $trackingPixelHtml;

            CampaignAudiencesEloquentModel::create([
                'secret' => $secretCode,
                'customer_id' => $data['customer_id'],
                'campaign_id' => $campaign->id
            ]);
            // $emailRecord = DB::table('email_records');
            // $emailRecord->insert([
            //     'secret' => $secretCode,
            //     'email' => $email
            // ]);
            try {

                Mail::to($data['email'])->send(new CampaignMail($email_content, $request->campaign_title));
            } catch (\Exception $e) {
            }
        }

        return response()->success('Email Successfully Send', 'success', Response::HTTP_OK);
    }

    //Tracking Customer Email
    public function trackEmailBySecret($secret)
    {
        // $emailRecord = DB::table('email_records')->where('secret', $secret)->update([
        //     'is_open' => 1
        // ]);

        CampaignAudiencesEloquentModel::where('secret', $secret)->update([
            'read_at' => Carbon::now()
        ]);
    }

    //Saving Upload Image in Storage For Email
    public function emailImageUpload(Request $request)
    {

        abort_if(authorize('send', CampaignManagementPolicy::class), Response::HTTP_FORBIDDEN, 'Need Send permission for Campaign Mail!');

        $fileName =  time() . '.' . $request->image->extension();

        $filePath = 'campaign_email_images/' . $fileName;

        Storage::disk('public')->put($filePath, file_get_contents($request->image));

        $returnUrl = asset('storage/campaign_email_images/' . $fileName);

        return response()->success($returnUrl, 'success', Response::HTTP_OK);
    }

    public function customersWithEmail()
    {
        abort_if(authorize('view', LeadPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for User!');

        try {

            return response()->success((new CustomersWithEmailQuery())->handle(), "Customer Lists", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function getCampaignList(Request $request)
    {
        abort_if(authorize('view', LeadPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for User!');

        try {

            $filters = $request->all();
            return response()->success((new CampaignListQuery($filters))->handle(), "Saleperson Lists", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
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
        abort_if(authorize('view', LeadPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for User!');

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

    public function getManagementOrManagerList()
    {
        try {

            $result = (new FindAllManagementOrManagerQuery())->handle();

            return response()->success($result, "Manager & Management Lists", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function groupSalepersonLeadManagementList(Request $request)
    {
        //check if user's has permission
        abort_if(authorize('view', LeadPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for User!');

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
        abort_if(authorize('view', LeadPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for User!');

        try {

            $filters = $request->all();
            $manager_id = $request->manager_id;

            // $saleperson = auth('sanctum')->user();

            // if ($saleperson->roles->contains('name', 'Salesperson')) {
            //     $staff_info = StaffEloquentModel::query()->where('user_id', $saleperson->id)->first();

            //     if (!$staff_info) {
            //         return response()->error(['sale_rank' => "Cannot show lead list because require saleperson info (Rank) doesn't existed."], "Saleperson Info Not Sufficient", 400);
            //     }
            // }

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

    public function staffExcelImport(Request $request)
    {
        try {

            $uploadFile = $request->file('staff_excel_file');

            // Convert spreadsheet to array
            $sheetsData = Excel::toArray([], $uploadFile);

            foreach ($sheetsData as $sheet) {
                $import = new StaffExcelImport();
                $import->collection(collect($sheet));
            }

            return response()->success(null, "Successfully Staff Excel Imported", Response::HTTP_OK);

        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

}
