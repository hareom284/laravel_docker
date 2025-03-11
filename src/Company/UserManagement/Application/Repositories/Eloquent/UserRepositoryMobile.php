<?php

namespace Src\Company\UserManagement\Application\Repositories\Eloquent;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Src\Company\UserManagement\Domain\Model\User;
use Src\Company\StaffManagement\Domain\Model\Staff;
use Src\Company\CustomerManagement\Domain\Model\Customer;
use Src\Company\UserManagement\Domain\Resources\UserResource;

use Src\Company\UserManagement\Application\Mappers\UserMapper;
use Src\Company\StaffManagement\Application\Mappers\StaffMapper;
use Src\Company\CompanyManagement\Domain\Services\QuickbookService;
use Src\Company\CustomerManagement\Application\Mappers\CustomerMapper;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;
use Src\Company\StaffManagement\Infrastructure\EloquentModels\StaffEloquentModel;
use Src\Company\System\Infrastructure\EloquentModels\GeneralSettingEloquentModel;
use Src\Company\UserManagement\Domain\Repositories\UserRepositoryMobileInterface;
use Src\Company\CustomerManagement\Infrastructure\EloquentModels\CheckListTemplateItemEloquentModel;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserMetaEloquentModel;

class UserRepositoryMobile implements UserRepositoryMobileInterface
{
    private $quickBookService;

    public function __construct(QuickbookService $quickBookService)
    {
        $this->quickBookService = $quickBookService;
    }

    public function store(User $user, $password, $roleIds, $salespersonIds, ?Customer $customer = null, ?Staff $staff = null): User
    {

        $userEloquent = UserMapper::toEloquent($user);

        $userEloquent->password = $password ? $password->value ?? $password : null;
        $commission = GeneralSettingEloquentModel::where('setting', 'commission')->first();
        $userEloquent->commission = $commission ? $commission->value : 0;
        $userEloquent->save();

        $userEloquent->roles()->sync($roleIds);

        if ($customer) {

            $salepersonArray = [];
            $checkListItemArray = [];
            foreach ($salespersonIds as $value) {
                $staff_info = StaffEloquentModel::where('user_id', $value)->with('user')->first();

                $salepersonName = $staff_info->user->first_name . ' ' . $staff_info->user->last_name;

                $customerName = $userEloquent->first_name . ' ' . $userEloquent->last_name;

                $salepersonEmail = $staff_info->user->email;

                // comment out this code because no longer needed to send mail to saleperson
                // $this->salepersonNotifyMail($salepersonName, $salepersonEmail, $customerName);

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

            // $customerEloquent->idMilestones()->attach($customerEloquent->id_milestone_id);

            $qboConfig = config('quickbooks');

            if ($qboConfig['qbo_integration']) {

                $customerName = $user->first_name . ' ' . $user->last_name;

                $type = $customerEloquent->customer_type ? 1 : 0;

                $quickBookCustomer = $this->quickBookService->getCusomter($customerName);

                if (!$quickBookCustomer) {

                    $customerEmail = $user->email;

                    $customerNo = $user->contact_no;

                    $customerData = [
                        'name' => $customerName,
                        'companyName' => ($type === 1) ? $customerName : null,
                        'email' => $customerEmail,
                        'address' => null,
                        'postal_code' => null,
                        'contact_no' => $customerNo
                    ];

                    $qboRecentCusomter = $this->quickBookService->saveOrGetQuickbookCustomer($customerData);

                    $userEloquent->quick_book_user_id = $qboRecentCusomter->Id;

                    $userEloquent->save();
                } else {

                    $userEloquent->quick_book_user_id = $quickBookCustomer->Id;

                    $userEloquent->save();
                }
            }
        } else if ($staff) {

            $staffEloquent = StaffMapper::toEloquent($staff);

            $staffEloquent->user_id = $userEloquent->id;
            $staffEloquent->rank_updated_at = Carbon::now();

            $staffEloquent->save();
        }

        return UserMapper::fromEloquent($userEloquent);
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

    public function getSalepersonList($filters = [])
    {

        $userEloquent = UserEloquentModel::query()->with('staffs')->with('projects')
        ->whereHas('roles', function ($query) {
            $query->where('role_id', 1);
        })
        ->where('is_active', true)
        ->orderBy('id', 'desc')
        ->get();

        $users = UserResource::collection($userEloquent);

        return $users;
    }

    public function updateProfile($user, $password, $id)
    {
        $userEloquent = UserEloquentModel::where('id', $id)->first();

        if (request()->hasFile('profile_pic')) {

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

        $updateData = [
            "name_prefix" => $user['name_prefix'],
            "first_name" => $user['first_name'],
            "last_name" => $user['last_name'],
            "email" => $user['email'],
            "contact_no" => $user['contact_no'],
            "prefix" => $user['prefix'],
            "password" => $password->value ?? $userEloquent->password
        ];


        // Only include profile_pic if it's not null
        if ($profilePic !== null) {
            $updateData['profile_pic'] = $profilePic;
        }

        $userEloquent->update($updateData);


        if (isset($signaturePic) || $user['registry_no'] != 'null') {
            $staff = $userEloquent->staffs;
            $staff->update([
                "signature" => $signaturePic,
                "registry_no" => $user['registry_no']
            ]);

            $pathOfSignature = Storage::disk('public')->get('staff_signature/' . $signaturePic);
            $userEloquent->signature = 'data:image/png;base64,' . base64_encode($pathOfSignature);
        }
        $userEloquent['profile_url'] = $userEloquent->profile_pic ? asset('storage/profile_pic/' . $userEloquent->profile_pic) : null;

        return $userEloquent;
    }

    public function updateDeviceId($data = [])
    {
        $userEloquent = UserEloquentModel::where('id', $data['id'])->first();

        $userEloquent->device_id = $data['device_id'];
        $userEloquent->update();

        return $userEloquent;

    }

    public function storeSurveyAnswer(array $data)
    {
        $surveyData = [
            'property_type' => $data['property_type'],
            'kitchen_work' => $data['kitchen_work'],
            'preferred_style' => $data['preferred_style'],
            'floor_plan_path' => $data['floor_plan'],
        ];

        UserMetaEloquentModel::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'name' => 'survey_answers',
            ],
            [
                'val' => json_encode($surveyData),
            ]
        );

        return $surveyData;
    }

    public function getSurveyAnswer()
    {
        Log::info('auth id '. auth()->id());
        $userMeta = UserMetaEloquentModel::where('user_id', auth()->id())->where('name', 'survey_answers')->first();
        return json_decode($userMeta);
    }

}
