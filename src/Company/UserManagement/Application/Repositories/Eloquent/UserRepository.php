<?php

namespace Src\Company\UserManagement\Application\Repositories\Eloquent;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Src\Company\UserManagement\Domain\Model\User;
use Src\Company\StaffManagement\Domain\Model\Staff;
use Src\Company\CustomerManagement\Domain\Model\Customer;
use Src\Company\UserManagement\Domain\Resources\UserResource;
use Src\Company\UserManagement\Application\Mappers\UserMapper;
use Src\Company\StaffManagement\Application\Mappers\StaffMapper;

use Src\Company\CompanyManagement\Domain\Services\QuickbookService;
use Src\Company\CustomerManagement\Application\Mappers\CustomerMapper;
use Src\Company\UserManagement\Domain\Repositories\UserRepositoryInterface;
use Src\Company\UserManagement\Infrastructure\EloquentModels\RoleEloquentModel;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;
use Src\Company\CompanyManagement\Domain\Repositories\AccountingServiceInterface;
use Src\Company\StaffManagement\Infrastructure\EloquentModels\StaffEloquentModel;
use Src\Company\System\Infrastructure\EloquentModels\GeneralSettingEloquentModel;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserMetaEloquentModel;
use Src\Company\CustomerManagement\Infrastructure\EloquentModels\CustomerEloquentModel;
use Src\Company\CustomerManagement\Infrastructure\EloquentModels\CheckListTemplateItemEloquentModel;

class UserRepository implements UserRepositoryInterface
{
    private $accountingService;

    public function __construct(AccountingServiceInterface $accountingService = null)
    {
        $this->accountingService = $accountingService;
    }

    public function getUsers($filters = [])
    {
        //user lists
        $perPage = $filters['perPage'] ?? 10;

        $userEloquent = UserEloquentModel::filter($filters)->with('staffs.mgr')->whereHas('roles', function ($query) {
            $query->whereNotIn('role_id', [5, 6]);
        })->orderBy('id', 'desc')->paginate($perPage);

        $users = UserResource::collection($userEloquent);

        $links = [
            'first' => $users->url(1),
            'last' => $users->url($users->lastPage()),
            'prev' => $users->previousPageUrl(),
            'next' => $users->nextPageUrl(),
        ];
        $meta = [
            'current_page' => $users->currentPage(),
            'from' => $users->firstItem(),
            'last_page' => $users->lastPage(),
            'path' => $users->url($users->currentPage()),
            'per_page' => $perPage,
            'to' => $users->lastItem(),
            'total' => $users->total(),
        ];
        $responseData['data'] = $users;
        $responseData['links'] = $links;
        $responseData['meta'] = $meta;

        return $responseData;
    }

    public function getUsersByRole()
    {
        $salesperson_id = RoleEloquentModel::query()->where('name', 'Salesperson')->firstOrFail()->id;

        $userEloquent = UserEloquentModel::query()->whereHas('roles', function ($query) use ($salesperson_id) {
            $query->where('role_id', $salesperson_id);
        })->get();

        return $userEloquent;
    }

    public function findUserById($id)
    {
        $user = UserEloquentModel::find($id);

        return $user;
    }

    public function findUserInfoById($id) // attempt to get all related data
    {
        $userEloquent = UserEloquentModel::query()->with('staffs.mgr')->findOrFail($id);

        $user = new UserResource($userEloquent);

        return $user;
    }

    public function store(User $user, $password, $roleIds, $salespersonIds, ?Customer $customer = null, ?Staff $staff = null): User
    {
        $userEloquent = UserMapper::toEloquent($user);

        $userEloquent->password = $password ? $password->value : null;

        $commission = GeneralSettingEloquentModel::where('setting', 'commission')->first();
    
        $userEloquent->commission = $commission ? $commission->value : 0;
        $userEloquent->save();

        $userEloquent->roles()->sync($roleIds);

        if ($customer) {

            $salepersonArray = [];

            $checkListItemArray = [];

            foreach ($salespersonIds as $value) {

                $staff_info = StaffEloquentModel::where('user_id', $value)->with('user')->first();

                $customerName = $userEloquent->first_name . ' ' . $userEloquent->last_name;

                array_push($salepersonArray, $staff_info->id);
            }

            $checkListItemEloquent = CheckListTemplateItemEloquentModel::all();

            foreach ($checkListItemEloquent as $checkListItem) {
                array_push($checkListItemArray, $checkListItem->id);
            }
            
            $customerEloquent = CustomerMapper::toEloquent($customer);

            $customerEloquent->user_id = $userEloquent->id;
            $customerEloquent->save();
            $customerEloquent->staffs()->sync($salepersonArray);

            $customerEloquent->leadCheckLists()->attach($checkListItemArray);
        } else if ($staff) {

            $staffEloquent = StaffMapper::toEloquent($staff);

            $staffEloquent->user_id = $userEloquent->id;
            $staffEloquent->rank_updated_at = Carbon::now();

            $staffEloquent->save();
        }

        return UserMapper::fromEloquent($userEloquent);
    }

    public function update(User $user, $roleIds, ?Customer $customer = null, ?Staff $staff = null)
    {

        $userEloquent = UserMapper::toEloquent($user);

        $userEloquent->save();

        $userEloquent->roles()->sync($roleIds);

        if ($customer) {

            $customerEloquent = CustomerMapper::toEloquent($customer);

            $customerEloquent->user_id = $userEloquent->id;

            $customerEloquent->save();

        } else if ($staff && $staff->rank_id) {

            StaffEloquentModel::updateOrCreate(
                ['user_id' => $userEloquent->id], // Conditions to match
                ['rank_id' => $staff->rank_id, 'mgr_id' => $staff->mgr_id, 'rank_updated_at' => Carbon::now()] // Values to update or create with
            );
        }
    }

    public function updateCustomerUser($id, $user, $password)
    {
        if(array_key_exists('profile_pic', $user))
        {
            if ($user['profile_pic'] !== "null") {

                $picName =  time() . '.' . $user['profile_pic']->extension();

                $filePath = 'profile_pic/' . $picName;

                Storage::disk('public')->put($filePath, file_get_contents($user['profile_pic']));

                $profile_pic = $picName;
            } elseif ($user['profile_pic'] === "null" && $user['original_pic'] === "null") {
                $profile_pic = null;
            } else {
                $profile_pic = isset($user['original_pic']) ? $user['original_pic'] : null;
            }
        }

        $userEloquent = UserEloquentModel::query()->where('id', $id)->first();

        $userEloquent->update([
            "profile_pic" => $profile_pic ?? null,
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'] ?? " ",
            'email' => $user['email'],
            'prefix' => $user['prefix'] ?? null,
            'contact_no' => $user['contact_no'],
            'name_prefix' => $user['name_prefix'] ?? null,
            'password' => $password ? $password->value : null
        ]);

        return $userEloquent;
    }

    public function updateProfile($user, $password, $id)
    {
        $userEloquent = UserEloquentModel::where('id', $id)->first();

        if ($user['profile_pic'] !== "null") {

            $picName =  time() . '.' . $user['profile_pic']->extension();

            $filePath = 'profile_pic/' . $picName;

            Storage::disk('public')->put($filePath, file_get_contents($user['profile_pic']));

            $profilePic = $picName;
        } elseif ($user['profile_pic'] === "null" && $user['original_pic'] === "null") {
            $profilePic = null;
        } else {
            $profilePic = isset($user['original_pic']) ? $user['original_pic'] : null;
        }

        // Update signature to staff record related to this user (probably needs to be in it's own UseCase)
        if ($user['signature'] !== "null") {

            $signatureName =  time() . '.' . $user['signature']->extension();

            $signatureFilePath = 'staff_signature/' . $signatureName;

            Storage::disk('public')->put($signatureFilePath, file_get_contents($user['signature']));

            $signaturePic = $signatureName;
        } elseif ($user['signature'] === "null" && $user['original_signature'] === "null") {
            $signaturePic = null;
        } else {
            $signaturePic = $userEloquent->staffs ? $userEloquent->staffs->signature : null;
        }

        $userEloquent->update([
            "profile_pic" => $profilePic,
            "name_prefix" => $user['name_prefix'],
            "first_name" => $user['first_name'],
            "last_name" => $user['last_name'],
            "email" => $user['email'],
            "contact_no" => $user['contact_no'],
            "prefix" => $user['prefix'],
            "password" => $password->value ?? $userEloquent->password
        ]);

        if (isset($signaturePic) || $user['registry_no']) {
            $staff = $userEloquent->staffs;
            $staff->update([
                "signature" => $signaturePic,
                "registry_no" => $user['registry_no']
            ]);

            $pathOfSignature = Storage::disk('public')->get('staff_signature/' . $signaturePic);
            $userEloquent->signature = 'data:image/png;base64,' . base64_encode($pathOfSignature);
        }

        return $userEloquent;
    }


    public function delete(int $user_id): void
    {
        $userEloquent = UserEloquentModel::query()->findOrFail($user_id);
        $userEloquent->roles()->detach();
        $userEloquent->delete();
    }

    public function getAllUsers()
    {
        return UserEloquentModel::whereHas('roles', function ($query) {
            $query->whereNotIn('role_id', [5, 6]);
        })->orderBy('id', 'desc')->get();
    }

    public function getSelectboxUsers()
    {
        return DB::table('users')
            ->orderBy('id', 'desc')
            ->select('id', 'first_name', 'last_name')
            ->get();
    }

    public function getManagerList()
    {
        $userEloquent = UserEloquentModel::whereHas('roles', function ($query) {
            $query->where('role_id', 8);
        })
        ->select('id', 'first_name', 'last_name')
        ->orderBy('id', 'desc')
        ->get();

        return $userEloquent;

    }

    public function getTeamMemberList()
    {
        $userEloquent = UserEloquentModel::query()
        ->whereDoesntHave('teams')
        ->whereHas('roles', function ($query) {
            $query->where('role_id', [1]);
        })
        ->select('id', 'first_name', 'last_name')
        ->orderBy('id', 'desc')
        ->get();

        return $userEloquent;
    }

    //Accounting Software Integration
    public function syncWithAccountingSoftwareData($companyId)
    {
        $leadsFromAccountingSoftware = $this->accountingService->getAllCustomers($companyId);

        $accountingSoftware = GeneralSettingEloquentModel::where('setting', 'accounting_software_integration')->first();

        $roleIds = ['5'];
        
        foreach ($leadsFromAccountingSoftware as $lead) {

            if($accountingSoftware->value === 'quickbooks'){

                $user = UserEloquentModel::create([
                    'first_name' => $lead->FullyQualifiedName,
                    'contact_no' => null,
                    'password' => null,
                    'is_active' => 1, 
                    'quick_book_user_id' => $lead->Id,
                ]);

                $user->roles()->sync($roleIds);

                CustomerEloquentModel::create([
                    'user_id' => $user->id,
                    'status' => 1
                ]);
            }

            if($accountingSoftware->value === 'xero'){

            }
            
        }
        
        return $accountingSoftware;
    }

    public function getSurveyByUserId($id)
    {
        $userMeta = UserMetaEloquentModel::where('user_id', $id)->where('name', 'survey_answers')->first();
        $surveyAnswers = json_decode($userMeta->val);
        $data = [
            'property_type' => $surveyAnswers->property_type,
            'kitchen_work' => $surveyAnswers->kitchen_work,
            'preferred_style' => $surveyAnswers->preferred_style,
            'floor_plan' => asset('storage/'. 'floor_plan/'.$surveyAnswers->floor_plan_path),
        ];
        return $data;
    }
}
