<?php

namespace Src\Company\System\Application\Repositories\Eloquent;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Src\Company\UserManagement\Application\Mappers\UserMapper;
use Src\Company\UserManagement\Domain\Model\User;
use Src\Company\System\Domain\Repositories\UserRepositoryInterface;
use Src\Company\CustomerManagement\Application\Mappers\CustomerMapper;
use Src\Company\StaffManagement\Application\Mappers\StaffMapper;
use Src\Company\StaffManagement\Domain\Model\Staff;
use Src\Company\CustomerManagement\Domain\Resources\CustomerResource;
use Src\Company\UserManagement\Domain\Resources\UserResource;
use Src\Company\StaffManagement\Domain\Resources\RankResource;
use Src\Company\CustomerManagement\Infrastructure\EloquentModels\CustomerEloquentModel;
use Src\Company\StaffManagement\Infrastructure\EloquentModels\RankEloquentModel;
use Src\Company\UserManagement\Infrastructure\EloquentModels\RoleEloquentModel;
use Src\Company\StaffManagement\Infrastructure\EloquentModels\StaffEloquentModel;
use Src\Company\CustomerManagement\Infrastructure\EloquentModels\CheckListTemplateItemEloquentModel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Src\Company\System\Domain\Mail\LeadCreateSuccessMail;
use Src\Company\System\Domain\Mail\NotifySalepersonMail;
use Src\Company\CompanyManagement\Domain\Services\QuickbookService;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\Exception\ServiceException;
use QuickBooksOnline\API\Facades\Customer as QuickbookCustomer;
use Illuminate\Support\Facades\Log;
use Src\Company\CustomerManagement\Domain\Model\Customer;
use Src\Company\Document\Infrastructure\EloquentModels\EvoEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\RenovationDocumentsEloquentModel;
use Src\Company\System\Domain\Resources\CampaingResource;
use Src\Company\CustomerManagement\Domain\Resources\CustomerWithEmailResource;
use Src\Company\System\Domain\Resources\DesignerListsForVendorFilterResource;
use Src\Company\CustomerManagement\Domain\Resources\GroupCustomerResources;
use Src\Company\CustomerManagement\Domain\Resources\GroupSalepersonLeadManagementResource;
use Src\Company\CustomerManagement\Domain\Resources\ManagerLeadManagementResources;
use Src\Company\System\Domain\Resources\SalepersonLeadManagementResource;
use Src\Company\System\Infrastructure\EloquentModels\CampaignEloquentModel;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;

class UserRepository implements UserRepositoryInterface
{
    private $quickBookService;

    public function __construct(QuickbookService $quickBookService)
    {
        $this->quickBookService = $quickBookService;
    }

    //Add for Cutomer Lists in both Superadmin and Management
    public function getCustomers($filters = [])
    {
        //user lists
        $perPage = $filters['perPage'] ?? 10;

        $userEloquent = UserEloquentModel::filter($filters)->with('customers.staffs.user')->whereHas('roles', function ($query) {
            $query->where('role_id', 5);
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

    public function getRanks()
    {
        $rankEloquent = RankEloquentModel::all();

        $rank = RankResource::collection($rankEloquent);

        return $rank;
    }

    public function getUsersByRole()
    {
        $salesperson_id = RoleEloquentModel::query()->where('name', 'Salesperson')->firstOrFail()->id;

        $userEloquent = UserEloquentModel::query()->whereHas('roles', function ($query) use ($salesperson_id) {
            $query->where('role_id', $salesperson_id);
        })->get();

        return $userEloquent;
    }

    public function getDrafters()
    {
        $userEloquent = UserEloquentModel::query()->with('staffs')->whereHas('roles', function ($query) {
            $query->where('role_id', 3);
        })
            ->where('is_active', true)
            ->get();

        $users = UserResource::collection($userEloquent);

        return $users;
    }

    public function getSalepersonList($filters = [])
    {
        //saleperson lists
        $perPage = $filters['perPage'] ?? '';

        if (isset($filters['mgr_id'])) {
            $userEloquent = UserEloquentModel::query()->with('staffs')->with('projects')
            ->whereHas('roles', function ($query) {
                $query->where('role_id', 1);
            })
            ->whereHas('staffs', function ($query) use ($filters) {
                $query->where('mgr_id', $filters['mgr_id']);
            })
            ->where('is_active', true)
            ->filter($filters)
            ->orderBy('id', 'desc')
            ->paginate($perPage);
        } else {
            $userEloquent = UserEloquentModel::query()->with('staffs')->with('projects')
            ->whereHas('roles', function ($query) {
                $query->where('role_id', 1);
            })
            ->where('is_active', true)
            ->filter($filters)
            ->orderBy('id', 'desc')
            ->paginate($perPage);
        }

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

    public function getSalepersonReportList()
    {
        //saleperson lists

        $userEloquent = UserEloquentModel::query()->with('staffs.rank')->whereHas('roles', function ($query) {
            $query->where('role_id', 1);
        })
        ->where('is_active', true)
        ->get();

        foreach ($userEloquent as $user) {

            $staff = $user->staffs;

            // Get the current year and month
            $currentYear = date('Y'); // Current year
            $currentMonth = date('n'); // Current month without leading zeros

            // Create Carbon instances for start and end of the current month
            // $startDate = Carbon::createFromDate(2024, 6, 1)->startOfMonth();
            // $endDate = Carbon::createFromDate(2024, 6, 1)->endOfMonth();

            $startDate = Carbon::createFromDate($currentYear, $currentMonth, 1)->startOfMonth();
            $endDate = Carbon::createFromDate($currentYear, $currentMonth, 1)->endOfMonth();

            $monthlyRenoDocs = RenovationDocumentsEloquentModel::with('projects.properties','projects.salespersons')->where('signed_by_salesperson_id',$staff->id)
                            ->whereNotNull('signed_date')
                            ->whereBetween('signed_date', [$startDate, $endDate])
                            ->get();

            $monthlyEvos = EvoEloquentModel::with('projects.properties','projects.salespersons')->where('signed_by_salesperson_id',$staff->id)
                    ->whereNotNull('signed_date')
                    ->whereBetween('signed_date', [$startDate, $endDate])
                    ->get();

            $renoMonthlyTotalSale = 0;
            $evoMonthlyTotalSale = $monthlyEvos->sum('grand_total');

            $renoMonthlyData = $monthlyRenoDocs->filter(function ($item) {
                return $item->type !== "FOC"; // Filter out items with type "FOC"
            })->map(function ($item) use (&$renoMonthlyTotalSale) {

                $totalCostingAmt = round($item->total_amount, 2);

                $renoMonthlyTotalSale += ($item->type === "CANCELLATION") ? (-1 * $totalCostingAmt) : $totalCostingAmt; // Subtract for "Cancellation"
            });

            $monthlyTotalSales = $renoMonthlyTotalSale + $evoMonthlyTotalSale;

            $user->monthlyData = round($monthlyTotalSales, 2);

            $yearlyRenoDocs = RenovationDocumentsEloquentModel::with('projects.properties','projects.salespersons')->where('signed_by_salesperson_id',$staff->id)
                            ->whereNotNull('signed_date')
                            ->whereYear('signed_date',$currentYear)
                            ->get();

            $yearlyEvos = EvoEloquentModel::with('projects.properties','projects.salespersons')->where('signed_by_salesperson_id',$staff->id)
                    ->whereNotNull('signed_date')
                    ->whereYear('signed_date',$currentYear)
                    ->get();

            $renoYearlyTotalSale = 0;
            $evoYearlyTotalSale = $yearlyEvos->sum('grand_total');

            $renoYearlySale = $yearlyRenoDocs->filter(function ($item) {
                return $item->type !== "FOC"; // Filter out items with type "FOC"
            })->map(function ($item) use (&$renoYearlyTotalSale) {

                $totalCostingAmt = round($item->total_amount, 2);

                $renoYearlyTotalSale += ($item->type === "CANCELLATION") ? (-1 * $totalCostingAmt) : $totalCostingAmt; // Subtract for "Cancellation"
            });

            $yearlyTotalSales = $renoYearlyTotalSale + $evoYearlyTotalSale;

            $user->yearlyData = round($yearlyTotalSales, 2);

        }

        $sortedUsers = $userEloquent->sortByDesc(function ($user) {
            return $user->monthlyData + $user->yearlyData;
        });

        $userWithMaxMonthlyData = new UserResource($userEloquent->sortByDesc('monthlyData')->first());

        $userWithMaxYearlyData = new UserResource($userEloquent->sortByDesc('yearlyData')->first());

        $users = UserResource::collection($sortedUsers);

        $data = [
            'monthly_highest' => $userWithMaxMonthlyData,
            'yearly_highest' => $userWithMaxYearlyData,
            'users' => $users,
        ];

        return $data;
    }

    public function getCustomerList($filters = [])
    {
        //customer lists
        $perPage = $filters['perPage'] ?? 10;

        $authId = Auth::user()->id;

        $staff_info = StaffEloquentModel::query()->where('user_id', $authId)->first();

        $userEloquent = UserEloquentModel::query()
            ->with('projects')
            ->with('customers', function ($query) {
                $query->with('customer_properties');
            })
            ->with('customers.leadCheckLists')
            ->whereHas('roles', function ($query) {
                $query->where('role_id', 5);
            })
            ->where('is_active', true)
            ->filter($filters)
            ->orderBy('id', 'desc');

        if (!empty($staff_info)) {
            $userEloquent->whereHas('customers.staffs', function ($query) use ($staff_info) {
                $query->where('salesperson_uid', $staff_info->id);
            });
        }

        $userEloquent = $userEloquent->paginate($perPage);
        // ->get();

        // dd($userEloquent->toArray());

        $users = CustomerResource::collection($userEloquent);

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

    public function findCustomerById($id)
    {
        $user_info = UserEloquentModel::query()->with('customers.staffs.user')->findOrFail($id);

        $user = new CustomerResource($user_info);

        return $user;
    }

    public function findCustomerBySalepersonId($id, $filters = [])
    {

        $perPage = $filters['perPage'] ?? 10;

        $statusMapping = [
            1 => 'lead',
            2 => 'home_owner',
            3 => 'complete',
            4 => 'inactive',
        ];

        $userEloquent = "";

        if (!$id) {
            if (isset($filters['saleperson'])) {
                $staff_info = StaffEloquentModel::query()->where('user_id', $filters['saleperson'])->first();

                $userEloquent = UserEloquentModel::query()->with('customers.assign_staff', 'customer_project.property')->with('customers.check_lists')->whereHas('roles', function ($query) {
                    $query->where('role_id', 5);
                })
                    ->whereHas('customers.staffs', function ($query) use ($staff_info) {
                        $query->where('salesperson_uid', $staff_info->id);
                    })
                    // ->where('is_active', true)
                    ->filter($filters)
                    // ->get();
                    ->paginate($perPage);
            } else {
                $staff_info = StaffEloquentModel::query()->where('user_id', $id)->first();

                $userEloquent = UserEloquentModel::query()->with('customers')->with('customers.check_lists')->whereHas('roles', function ($query) {
                    $query->where('role_id', 5);
                })
                    ->filter($filters)
                    // ->get();
                    ->paginate($perPage);
            }
        } else {

            $staff_info = StaffEloquentModel::query()->where('user_id', $id)->first();

            $userEloquent = UserEloquentModel::query()->with('customers.assign_staff', 'customer_project.property')->with('customers.check_lists')->whereHas('roles', function ($query) {
                $query->where('role_id', 5);
            })
                ->filter($filters)
                ->whereHas('customers.staffs', function ($query) use ($staff_info) {
                    $query->where('salesperson_uid', $staff_info->id);
                })
                // ->where('is_active', true)
                ->filter($filters)
                // ->get();
                ->paginate($perPage);
        }

        $user = CustomerResource::collection($userEloquent);

        $userReformat = $user->groupBy(function ($user) use ($statusMapping) {
            $statusNumber = $user->customers->status ?? 'Unknown';
            return $statusMapping[$statusNumber] ?? 'Unknown'; // Default to 'Unknown' if no mapping is found
        });

        $links = [
            'first' => $user->url(1),
            'last' => $user->url($user->lastPage()),
            'prev' => $user->previousPageUrl(),
            'next' => $user->nextPageUrl(),
        ];
        $meta = [
            'current_page' => $user->currentPage(),
            'from' => $user->firstItem(),
            'last_page' => $user->lastPage(),
            'path' => $user->url($user->currentPage()),
            'per_page' => $perPage,
            'to' => $user->lastItem(),
            'total' => $user->total(),
        ];
        $responseData['data'] = $userReformat;
        $responseData['links'] = $links;
        $responseData['meta'] = $meta;

        return $responseData;
    }

    public function findCustomerByManagerId($id, $filters = [])
    {

        $perPage = $filters['perPage'] ?? 10;

        $statusMapping = [
            1 => 'lead',
            2 => 'home_owner',
            3 => 'complete',
            4 => 'inactive',
        ];

        $userEloquent = "";

        $staff_info = StaffEloquentModel::query()->where('mgr_id', $id)->get();

        $staffIds = $staff_info->pluck('id')->toArray();

        $userEloquent = UserEloquentModel::query()->with('customers.assign_staff', 'customer_project.property')->with('customers.check_lists')->whereHas('roles', function ($query) {
            $query->where('role_id', 5);
        })
            ->filter($filters)
            ->whereHas('customers.staffs', function ($query) use ($staffIds) {
                $query->whereIn('salesperson_uid', $staffIds);
            })
            // ->where('is_active', true)
            ->filter($filters)
            // ->get();
            ->paginate($perPage);

        $user = CustomerResource::collection($userEloquent);

        $userReformat = $user->groupBy(function ($user) use ($statusMapping) {
            $statusNumber = $user->customers->status ?? 'Unknown';
            return $statusMapping[$statusNumber] ?? 'Unknown'; // Default to 'Unknown' if no mapping is found
        });

        $links = [
            'first' => $user->url(1),
            'last' => $user->url($user->lastPage()),
            'prev' => $user->previousPageUrl(),
            'next' => $user->nextPageUrl(),
        ];
        $meta = [
            'current_page' => $user->currentPage(),
            'from' => $user->firstItem(),
            'last_page' => $user->lastPage(),
            'path' => $user->url($user->currentPage()),
            'per_page' => $perPage,
            'to' => $user->lastItem(),
            'total' => $user->total(),
        ];
        $responseData['data'] = $userReformat;
        $responseData['links'] = $links;
        $responseData['meta'] = $meta;

        return $responseData;
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

            $qboConfig = config('quickbooks');

            if ($qboConfig['qbo_integration']) {

                $customerName = $user->first_name . ' ' . $user->last_name;

                $type = $customerEloquent->customer_type ? 1 : 0;

                $quickBookCustomer = $this->quickBookService->getCustomer(1, $customerName);

                if(!$quickBookCustomer){

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

                }else{

                    $userEloquent->quick_book_user_id = $quickBookCustomer->Id;

                    $userEloquent->save();
                }

            }
        } else if ($staff) {

            $staffEloquent = StaffMapper::toEloquent($staff);

            $staffEloquent->user_id = $userEloquent->id;

            $staffEloquent->save();
        }

        return UserMapper::fromEloquent($userEloquent);
    }

    public function customerUpdate($id, $user, $password)
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

        $userEloquent = UserEloquentModel::query()->where('id', $id)->first();

        $oldCustomerName = $userEloquent->first_name . ' ' . $userEloquent->last_name;
        $oldCustomerEmail = $userEloquent->email;
        $oldCustomerContactNo = $userEloquent->contact_no;

        $userEloquent->update([
            "profile_pic" => $profile_pic,
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'] ?? " ",
            'email' => $user['email'],
            'contact_no' => $user['contact_no'],
            'name_prefix' => $user['name_prefix'],
            'password' => $password ? $password->value : null
        ]);

        // $user_id = auth('sanctum')->user()->id;

        // $salespersonIdsJson = $user['saleperson_ids']; // Access the 'saleperson_ids' JSON string

        // // Decode the JSON string into a PHP array
        // $salespersonArray = [];
        // $salespersonIds = json_decode($salespersonIdsJson, true);
        // //map the salesperson id to the staff id as it will be needed for syncing customer to salesperson
        // foreach ($salespersonIds as $value) {
        //     $staff_info = StaffEloquentModel::where('user_id', $value)->with('user')->first();

        //     array_push($salespersonArray, $staff_info->id);
        // }

        // $customerEloquent = CustomerEloquentModel::query()->where('user_id', $id)->first();
        // // sync the salesperson(s) to customer
        // $customerEloquent->staffs()->sync($salespersonArray);

        CustomerEloquentModel::query()->where('user_id', $id)->first()->update([
            'nric' => $user['nric'],
            'source' => $user['source'] ?? null,
            'additional_information' => $user['additional_information'],
            'last_modified_by' => $user_id,
            'company_name' => $user['company_name'],
            'customer_type' => $user['customer_type'],
            'budget' => $user['budget'],
            'quote_value' => $user['quote_value'],
            'book_value' => $user['book_value'],
            'key_collection' => $user['key_collection'],
            'id_milestone' => $user['id_milestone'],
            'rejected_reason' => $user['rejected_reason'],
            'next_meeting' => $user['next_meeting'],
            'days_aging' => $user['days_aging'],
            'remarks' => $user['remarks'],
            'budget_value' => $user['budget_value'],
        ]);

        $properties = json_decode($user['properties'], true);

        $cusAddress = $properties[0]['block_num'] . ' ' . $properties[0]['street_name'] . ' #' . $properties[0]['unit_num'] ;
        $cusPostalCode = $properties[0]['postal_code'];

        $qboConfig = config('quickbooks');

        if ($qboConfig['qbo_integration']) {

            $customerName = $userEloquent->first_name . ' ' . $userEloquent->last_name;
            $type = $customerEloquent->customer_type ? 1 : 0;
            $customerEmail = $userEloquent->email;
            $customerNo = $userEloquent->contact_no;

            if(is_null($userEloquent->quick_book_user_id)){

                $customerData = [
                    'name' => $customerName,
                    'companyName' => ($type === 1) ? $customerName : null,
                    'email' => $customerEmail,
                    'address' => $cusAddress,
                    'postal_code' => $cusPostalCode,
                    'contact_no' => $customerNo
                ];

                $quickBookCustomer = $this->quickBookService->saveOrGetQuickbookCustomer($customerData);

                $userEloquent->quick_book_user_id = $quickBookCustomer->Id;

                $userEloquent->save();

            }else{

                $customerData = [];

                if($oldCustomerName !== $customerName){
                    $customerData['name'] = $customerName;
                }

                if($oldCustomerEmail !== $customerEmail){
                    $customerData['email'] = $customerEmail;
                }

                if($oldCustomerContactNo !== $customerNo){
                    $customerData['contact_no'] = $customerNo;
                }

                $customerData['companyName'] = ($type === 1) ? $customerName : null;
                $customerData['address'] = $cusAddress;
                $customerData['postal_code'] = $cusPostalCode;

                $this->quickBookService->updateCustomer($userEloquent->quick_book_user_id,$customerData);
            }
        }

        return $userEloquent;
    }

    public function update(User $user, $roleIds, ?Customer $customer = null, ?Staff $staff = null): void
    {
        $userEloquent = UserMapper::toEloquent($user);

        $userEloquent->save();

        $userEloquent->roles()->sync($roleIds);

        if ($customer) {
            $customerEloquent = CustomerMapper::toEloquent($customer);

            $customerEloquent->user_id = $userEloquent->id;

            $customerEloquent->save();
        } else if ($staff && $staff->rank_id) {
            // $staffEloquent = StaffEloquentModel::where('user_id',$userEloquent->id)->first();

            // $staffEloquent->user_id = $userEloquent->id;

            // $staffEloquent->rank_id = $staff->rank_id;

            // $staffEloquent->save();

            StaffEloquentModel::updateOrCreate(
                ['user_id' => $userEloquent->id], // Conditions to match
                ['rank_id' => $staff->rank_id, 'mgr_id' => $staff->mgr_id] // Values to update or create with
            );
        }
    }

    public function updateProfile($user, $password, $id)
    {
        $userEloquent = UserEloquentModel::query()->with('customers')->where('id', $id)->first();

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
            "password" => $password->value ?? $userEloquent->password
        ]);

        if(isset($userEloquent->customers) && $user['nric']){
            $userEloquent->customers->update([
                'nric' => $user['nric']
            ]);
        };

        if (isset($signaturePic)) {
            $staff = $userEloquent->staffs;
            $staff->update([
                "signature" => $signaturePic
            ]);

            $pathOfSignature = Storage::disk('public')->get('staff_signature/' . $signaturePic);
            $userEloquent->signature = 'data:image/png;base64,' . base64_encode($pathOfSignature);
        }

        return $userEloquent;
    }

    public function inactive(int $user_id): void
    {
        $inactive_reason = request()->inactive_reason;
        $userEloquent = UserEloquentModel::query()->findOrFail($user_id);
        $userEloquent->is_active = 0;
        $userEloquent->save();

        $customer = CustomerEloquentModel::query()->where('user_id', $user_id)->first();
        $currentDate = Carbon::now();
        if ($customer) {
            $customer->update([
                'status' => 4,
                'inactive_at' => $currentDate,
                'inactive_reason' => $inactive_reason
            ]);
        }
    }

    public function active(int $user_id)
    {
        $userEloquent = UserEloquentModel::query()->with('customer_project')->findOrFail($user_id);
        $userEloquent->is_active = 1;
        $userEloquent->save();

        $customer = CustomerEloquentModel::query()->where('user_id', $user_id)->first();

        if ($customer) {
            if ($userEloquent->customer_project == null || $userEloquent->customer_project->project_status == "New") {
                $customer->update([
                    'status' => 1,
                ]);
            } else if ($userEloquent->customer_project->project_status == "InProgress") {
                $customer->update([
                    'status' => 2,
                ]);
            } else if ($userEloquent->customer_project->project_status == "Completed") {
                $customer->update([
                    'status' => 3,
                ]);
            }
            $customer->update([
                'inactive_at' => null,
                'inactive_reason' => null
            ]);
        }
    }

    public function salepersonNotifyMail($salepersonName, $salepersonEmail, $customerName)
    {
        Mail::to($salepersonEmail)->send(new NotifySalepersonMail($salepersonName, $customerName));

        return true;
    }

    public function assignRank(int $salepersonId, int $rankId): void
    {
        $saleperson = StaffEloquentModel::query()->where('user_id', $salepersonId)->first();

        $saleperson->rank_id = $rankId;
        $saleperson->rank_updated_at = Carbon::now();

        $saleperson->update();
    }

    public function sendSuccessMail($name, $email, $password, $siteSetting, $salespersonNames)
    {
        $user = auth('sanctum')->user();

        $salespersonName = $user->first_name . $user->last_name;

        Mail::to($email)->send(new LeadCreateSuccessMail($name, $email, $password, $siteSetting, $salespersonNames));

        return true;
    }

    public function updateCheckListStatus($data)
    {
        $customer = CustomerEloquentModel::find($data['customer_id']);

        $currentDate = now()->format('Y-m-d');

        $date = $data['status'] ? $currentDate : null;

        $customer->leadCheckLists()->updateExistingPivot($data['checklist_template_item_id'], ['status' => $data['status'], 'date_completed' => $date]);
    }

    public function getCustomersWithEmail()
    {
        $users = UserEloquentModel::whereHas('customers')
            ->with('customers')
            ->whereNotNull('email')->orderBy('id', 'desc')
            ->select('id', 'first_name', 'last_name', 'email')->get();

        return CustomerWithEmailResource::collection($users);
    }

    public function getCampaingList($filters = [])
    {


        //saleperson lists
        $perPage = $filters['perPage'] ?? '';

        $campaingEloquent = CampaignEloquentModel::with('campaignAudiences')->filter($filters)
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        $campaings = CampaingResource::collection($campaingEloquent);

        $links = [
            'first' => $campaings->url(1),
            'last' => $campaings->url($campaings->lastPage()),
            'prev' => $campaings->previousPageUrl(),
            'next' => $campaings->nextPageUrl(),
        ];
        $meta = [
            'current_page' => $campaings->currentPage(),
            'from' => $campaings->firstItem(),
            'last_page' => $campaings->lastPage(),
            'path' => $campaings->url($campaings->currentPage()),
            'per_page' => $perPage,
            'to' => $campaings->lastItem(),
            'total' => $campaings->total(),
        ];
        $responseData['data'] = $campaings;
        $responseData['links'] = $links;
        $responseData['meta'] = $meta;

        return $responseData;
    }

    public function getLeadManagementReport($data = [])
    {

        $status = $data['filters']['status'];
        $leadSource = $data['filters']['lead_source'];
        $startDate = $data['filters']['start_date'];
        $endDate = $data['filters']['end_date'];

        if (isset($data['saleperson_id'])) {
            $staff_info = StaffEloquentModel::query()->with('mgr', 'user')->where('user_id', $data['saleperson_id'])->first();

            $customerEloquent = CustomerEloquentModel::with('user','currentIdMilestone','rejectedReason')->whereHas('staffs', function ($query) use ($staff_info) {
                $query->where('salesperson_uid', $staff_info->id);
            })
                ->when(isset($startDate) && isset($endDate), function ($query) use ($startDate, $endDate) {
                    $query->whereHas('user', function ($query) use ($startDate, $endDate) {
                        $query->whereBetween('created_at', [$startDate, $endDate]);
                    });
                })
                ->when($status ?? false, function ($query, $status) {
                    $query->where('id_milestone_id', $status);
                })
                ->when($leadSource ?? false, function ($query, $source) {
                    $query->where('source', $source);
                })
                ->get();
        } else if (isset($data['manager_id'])) {
            $staff_info = StaffEloquentModel::query()->with('mgr', 'user')->where('mgr_id', $data['manager_id'])->get();

            $staffIds = $staff_info->pluck('id')->toArray();

            $customerEloquent = CustomerEloquentModel::with('user','currentIdMilestone','rejectedReason')->whereHas('staffs', function ($query) use ($staffIds) {
                $query->whereIn('salesperson_uid', $staffIds);
            })
                ->when(isset($startDate) && isset($endDate), function ($query) use ($startDate, $endDate) {
                    $query->whereHas('user', function ($query) use ($startDate, $endDate) {
                        $query->whereBetween('created_at', [$startDate, $endDate]);
                    });
                })
                ->when($status ?? false, function ($query, $status) {
                    $query->where('id_milestone_id', $status);
                })
                ->when($leadSource ?? false, function ($query, $source) {
                    $query->where('source', $source);
                })
                ->get();
        } else {
            $customerEloquent = CustomerEloquentModel::with('user', 'staffs.mgr','currentIdMilestone','rejectedReason')->whereHas('staffs')
                ->when(isset($startDate) && isset($endDate), function ($query) use ($startDate, $endDate) {
                    $query->whereHas('user', function ($query) use ($startDate, $endDate) {
                        $query->whereBetween('created_at', [$startDate, $endDate]);
                    });
                })
                ->when($status ?? false, function ($query, $status) {
                    $query->where('id_milestone_id', $status);
                })
                ->when($leadSource ?? false, function ($query, $source) {
                    $query->where('source', $source);
                })
                ->get();
        }

        // $customerEloquent = collect($customerData)->where('source',$leadSource)->where('id_milestone',$status);

        // Transform the collection to extract milestone names
        $transformedCustomerData = collect($customerEloquent)->map(function ($customer) {
            $customer['milestone_name'] = $customer->currentIdMilestone ? $customer->currentIdMilestone->name : 'No Milestone';
            $customer['rejected_reason'] = $customer->rejectedReason ? $customer->rejectedReason->name : 'No Rejected Reason';
            return $customer;
        });

        // Group by the milestone name
        $id_milestones = $transformedCustomerData->groupBy('milestone_name');

        // $id_milestones = collect($customerEloquent)->groupBy('id_milestone_id');

        $id_milestones_array = [];

        foreach ($id_milestones as $key => $value) {

            $id_milestones_initial = [
                "label" => $key,
                "count" => count($id_milestones[$key])
            ];

            array_push($id_milestones_array, $id_milestones_initial);
        }

        // $rejected_reason = collect($customerEloquent)->groupBy('rejected_reason');

        $rejected_reason = $transformedCustomerData->groupBy('rejected_reason');

        $rejected_reason_array = [];

        foreach ($rejected_reason as $key => $value) {

            $rejected_reason_initial = [
                "label" => $key,
                "count" => count($rejected_reason[$key])
            ];

            array_push($rejected_reason_array, $rejected_reason_initial);
        }

        $initial_array = [];

        foreach ($customerEloquent as &$item) {
            // Check if the user exists in the item
            if (isset($item['user'])) {
                // Get the user object
                $user = $item['user'];

                array_push($initial_array, [
                    'created_at' => $user['created_at']->format('F'),
                    'quote_value' => $item->quote_value,
                    'book_value' => $item->book_value
                ]);
            }
        }

        $quote_book_values = collect($initial_array)->groupBy('created_at');
        $monthlyTotal = [];

        // Iterate through each month
        foreach ($quote_book_values as $month => $items) {
            $totalQuoteValue = 0;
            $totalBookValue = 0;

            // Iterate through the items for the current month
            foreach ($items as $item) {
                $totalQuoteValue += $item['quote_value'];
                $totalBookValue += $item['book_value'];
            }

            // Store the total values for the current month
            $monthlyTotal[$month] = [
                'quote_value_total' => $totalQuoteValue,
                'book_value_total' => $totalBookValue
            ];
        }

        $initial_array1 = [];

        if (isset($data['saleperson_id'])) {
            foreach ($customerEloquent as &$item) {
                // Check if the user exists in the item
                if (isset($item['user'])) {
                    // Get the user object
                    $user = $item['user'];

                    array_push($initial_array1, [
                        'created_at' => isset($staff_info->user) ? $staff_info->user->first_name . ' ' . $staff_info->user->last_name : '',
                        'quote_value' => $item->quote_value,
                        'book_value' => $item->book_value
                    ]);
                }
            }
        } else if (isset($data['manager_id'])) {
            foreach ($customerEloquent as &$item) {
                // Check if the user exists in the item
                if (isset($item['user'])) {
                    // Get the user object
                    $user = $item['user'];

                    array_push($initial_array1, [
                        'created_at' => isset($staff_info[0]->mgr) ? $staff_info[0]->mgr->first_name . ' ' . $staff_info[0]->mgr->last_name : '',
                        'quote_value' => $item->quote_value,
                        'book_value' => $item->book_value
                    ]);
                }
            }
        } else {
            foreach ($customerEloquent as &$item) {
                // Check if the user exists in the item
                if (isset($item['user'])) {

                    foreach ($item->staffs as $staff) {

                        if (isset($staff->mgr)) {
                            array_push($initial_array1, [
                                'created_at' => isset($staff->mgr) ? $staff->mgr->first_name . ' ' . $staff->mgr->last_name : '',
                                'quote_value' => $item->quote_value,
                                'book_value' => $item->book_value
                            ]);
                        }
                    }
                }
            }
        }

        $mgr_values = collect($initial_array1)->groupBy('created_at');
        $mgrTotal = [];

        // Iterate through each month
        foreach ($mgr_values as $month => $items) {
            $totalQuoteValue = 0;
            $totalBookValue = 0;

            // Iterate through the items for the current month
            foreach ($items as $item) {
                $totalQuoteValue += $item['quote_value'];
                $totalBookValue += $item['book_value'];
            }

            // Store the total values for the current month
            $mgrTotal[$month] = [
                'quote_value_total' => $totalQuoteValue,
                'book_value_total' => $totalBookValue
            ];
        }

        $final_result = [];
        $final_result['id_milestones'] = $id_milestones_array;
        $final_result['client_milestones'] = $rejected_reason_array;
        $final_result['quote_book_values'] = $monthlyTotal;
        $final_result['mgr_total'] = $mgrTotal;

        return $final_result;
    }

    public function getSalepersonLeadManagementList($id, $filters = [])
    {

        $perPage = $filters['perPage'] ?? 10;
        $staff_info = StaffEloquentModel::query()->where('user_id', $id)->first();


        $userEloquent = UserEloquentModel::query()->with('customers','projectPivot.customersPivot')->whereHas('roles', function ($query) {
            $query->where('role_id', 5);
        })->filter($filters)
        ->whereHas('customers.staffs', function ($query) use ($staff_info) {
            $query->where('salesperson_uid', $staff_info->id);
        })
        ->filter($filters)
        ->paginate($perPage);

        $totalQuoteValue = 0;
        $totalBookValue = 0;
        $totalBudgetValue = 0;
        $totalCxCount = 0;

        foreach ($userEloquent as $user) {
            $totalQuoteValue += $user->customers->quote_value ?? 0;
            $totalBookValue += $user->customers->book_value ?? 0;
            $totalBudgetValue += $user->customers->budget_value ?? 0;

            // making the array of customersPivot to empty if the lead is not the first person of the project
            if(count($user->projectPivot) > 0){ // temporary solution to fix Lead List Page issue
                if(count($user->projectPivot[0]->customersPivot) > 1){
                    $userId = $user->projectPivot[0]->customersPivot[0]->id;

                    if($user->id !== $userId)
                    {
                        $user->projectPivot[0]->customersPivot = [];
                    }

                }

                $totalCxCount += count($user->projectPivot[0]->customersPivot);
            }
        }
        $user = SalepersonLeadManagementResource::collection($userEloquent);

        $links = [
            'first' => $user->url(1),
            'last' => $user->url($user->lastPage()),
            'prev' => $user->previousPageUrl(),
            'next' => $user->nextPageUrl(),
        ];
        $meta = [
            'current_page' => $user->currentPage(),
            'from' => $user->firstItem(),
            'last_page' => $user->lastPage(),
            'path' => $user->url($user->currentPage()),
            'per_page' => $perPage,
            'to' => $user->lastItem(),
            'total' => $user->total(),
            'total_quote_value' => $totalQuoteValue,
            'total_book_value' => $totalBookValue,
            'total_budget_value' => $totalBudgetValue,
            'total_cx_count_value' => $totalCxCount,
        ];
        $responseData['data'] = $user;
        $responseData['links'] = $links;
        $responseData['meta'] = $meta;

        return $responseData;
    }

    public function getDesignerListsForVendorFilter()
    {
        $designerLists = UserEloquentModel::whereHas('roles', function ($query) {
            $query->where('role_id', 1);
        })->where('is_active', true)
        ->get();

        return DesignerListsForVendorFilterResource::collection($designerLists);
    }

    public function getManagementOrManger()
    {
        $userEloquent = UserEloquentModel::query()->with('staffs')->whereHas('roles', function ($query) {
            $query->where('role_id', 2)->orWhere('role_id', 8);
        })->get();

        $users = UserResource::collection($userEloquent);

        return $users;
    }

    public function getGroupSalepersonLeadManagementList($mgr_id, $filters = [])
    {

        $perPage = $filters['perPage'] ?? 10;
        $status = $filters['status'];
        $leadSource = $filters['lead_source'];
        $startDate = $filters['start_date'];
        $endDate = $filters['end_date'];

        $userEloquent = CustomerEloquentModel::with('user.projectPivot.customersPivot', 'staffs.mgr','staffs.user')
            ->when(isset($startDate) && isset($endDate), function ($query) use ($startDate, $endDate) {
                $query->whereHas('user', function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                });
            })
            ->when($status ?? false, function ($query, $status) {
                $query->where('id_milestone_id', $status);
            })
            ->when($leadSource ?? false, function ($query, $source) {
                $query->where('source', $source);
            })
            ->paginate($perPage);

        $customers = collect($userEloquent->items());

        foreach ($customers as $customer) {
            if(count($customer->user->projectPivot) > 0){
                if(count($customer->user->projectPivot[0]->customersPivot) > 1){
                    $userId = $customer->user->projectPivot[0]->customersPivot[0]->id;

                    if($customer->user->id !== $userId)
                    {
                        $customer->user->projectPivot[0]->customersPivot = [];
                    }

                }
            }
        }

        $user = $customers->flatMap(function ($customer) {

            $staffs = $customer['staffs'];

            if (count($staffs) <= 0) {
                // If staffs array is empty, include the customer with an empty staff
                return [
                    [
                        'mgr' => (object)[],
                        'staff' => (object)[],
                        'customer' => $customer,
                    ]
                ];
            }

            return collect($customer['staffs'])->map(function ($staff) use ($customer) {
                return [
                    'mgr' => $staff['mgr'] ?? (object)[],
                    'staff' => $staff,
                    'customer' => $customer,
                ];
            });
        })->groupBy(function ($item) {
            $mgr = $item['mgr'];
            return empty((array)$mgr) ? '' : $mgr['first_name'] . ' ' . $mgr['last_name'];
        })->map(function ($items, $mgrName) {
            $mgr = $items->first()['mgr'];
            $assigned_salepersons = $items->groupBy('staff.id')->map(function ($staffItems) {
                $staff = $staffItems->first()['staff'];
                $sale = count((array)$staff) > 0 ? $staff['user'] : [];

                $customers = $staffItems->pluck('customer')->unique('id')->values();
                // Calculate financials and counts
                $total_quote_value = $customers->sum('quote_value');
                $total_budget_value = $customers->sum('budget_value');
                $total_book_value = $customers->sum('book_value');
                $customer_count = $customers->count();

                return (object)[
                    'id' => isset($sale['id']) ? $sale['id'] : '' ,
                    'first_name' => isset($sale['first_name']) ? $sale['first_name'] : '',
                    'last_name' => isset($sale['last_name']) ? $sale['last_name'] : '',
                    'name_prefix' => isset($sale['name_prefix']) ? $sale['name_prefix'] : '',
                    'customers' => GroupCustomerResources::collection($customers),
                    'total_quote_value' => $total_quote_value,
                    'total_budget_value' => $total_budget_value,
                    'total_book_value' => $total_book_value,
                    'customer_count' => $customer_count,
                ];
            })->values();

            return (object)[
                'id' => empty((array)$mgr) ? '' : $mgr->id,
                'first_name' => empty((array)$mgr) ? '' : $mgr->first_name,
                'last_name' => empty((array)$mgr) ? '' : $mgr->last_name,
                'name_prefix' => empty((array)$mgr) ? '' : $mgr->name_prefix,
                'assigned_salepersons' => $assigned_salepersons,
            ];
        })->values()->all();

        // Calculate Grand Totals
        $grandTotalQuoteValue = 0;
        $grandTotalBudgetValue = 0;
        $grandTotalBookValue = 0;
        $grandTotalCustomerCount = 0;

        $uniqueCustomerIds = collect();

        foreach ($user as $group) {
            foreach ($group->assigned_salepersons as $salesperson) {
                $grandTotalQuoteValue += $salesperson->total_quote_value;
                $grandTotalBudgetValue += $salesperson->total_budget_value;
                $grandTotalBookValue += $salesperson->total_book_value;

                foreach ($salesperson->customers as $customer) {
                    if (!$uniqueCustomerIds->contains($customer->id)) {
                        $uniqueCustomerIds->push($customer->id);
                        $grandTotalCustomerCount++;
                    }
                }
            }
        }

        $links = [
            'first' => $userEloquent->url(1),
            'last' => $userEloquent->url($userEloquent->lastPage()),
            'prev' => $userEloquent->previousPageUrl(),
            'next' => $userEloquent->nextPageUrl(),
        ];
        $meta = [
            'current_page' => $userEloquent->currentPage(),
            'from' => $userEloquent->firstItem(),
            'last_page' => $userEloquent->lastPage(),
            'path' => $userEloquent->url($userEloquent->currentPage()),
            'per_page' => $perPage,
            'to' => $userEloquent->lastItem(),
            'total' => $userEloquent->total(),
            'total_quote_value' => $grandTotalQuoteValue,
            'total_book_value' => $grandTotalBudgetValue,
            'total_budget_value' => $grandTotalBookValue,
            'total_customers_count' => $grandTotalCustomerCount,
        ];
        $responseData['data'] = $user;
        $responseData['links'] = $links;
        $responseData['meta'] = $meta;

        return $responseData;
    }


    public function getManagerLeadManagementList($id, $filters = [])
    {
        $perPage = $filters['perPage'] ?? 10;
        $staff_info = StaffEloquentModel::query()->where('mgr_id', $id)->get();

        $staffIds = $staff_info->pluck('id')->toArray();

        $userEloquent = UserEloquentModel::query()->with('customers','projectPivot.customersPivot')->whereHas('roles', function ($query) {
            $query->where('role_id', 5);
        })->filter($filters)
            ->whereHas('customers.staffs', function ($query) use ($staffIds) {
                $query->whereIn('salesperson_uid', $staffIds);
            })
            ->filter($filters)
            ->paginate($perPage);

        $totalQuoteValue = 0;
        $totalBookValue = 0;
        $totalBudgetValue = 0;
        $totalCxCount = 0;

        foreach ($userEloquent as $user) {
            $totalQuoteValue += $user->customers->quote_value ?? 0;
            $totalBookValue += $user->customers->book_value ?? 0;
            $totalBudgetValue += $user->customers->budget_value ?? 0;

            // making the array of customersPivot to empty if the lead is not the first person of the project
            if(count($user->projectPivot[0]->customersPivot) > 1){
                $userId = $user->projectPivot[0]->customersPivot[0]->id;

                if($user->id !== $userId)
                {
                    $user->projectPivot[0]->customersPivot = [];
                }

            }

            $totalCxCount += count($user->projectPivot[0]->customersPivot);

        }
        $user = ManagerLeadManagementResources::collection($userEloquent);

        $links = [
            'first' => $user->url(1),
            'last' => $user->url($user->lastPage()),
            'prev' => $user->previousPageUrl(),
            'next' => $user->nextPageUrl(),
        ];
        $meta = [
            'current_page' => $user->currentPage(),
            'from' => $user->firstItem(),
            'last_page' => $user->lastPage(),
            'path' => $user->url($user->currentPage()),
            'per_page' => $perPage,
            'to' => $user->lastItem(),
            'total' => $user->total(),
            'total_quote_value' => $totalQuoteValue,
            'total_book_value' => $totalBookValue,
            'total_budget_value' => $totalBudgetValue,
            'total_cx_count_value' => $totalCxCount,
        ];
        $responseData['data'] = $user;
        $responseData['links'] = $links;
        $responseData['meta'] = $meta;

        return $responseData;
    }

    public function updateIdMilestone($data = [])
    {

        foreach ($data['user_id'] as $user_id) {
            CustomerEloquentModel::query()->where('user_id', $user_id)->first()->update([
                'id_milestone_id' => $data['id_milestone_id']
            ]);
        }

        return "Success";
    }

    public function syncLeadWithQuickbook()
    {
        $qboCustomers = $this->quickBookService->getAllCustomers();

        $roleIds = ['5'];

        foreach ($qboCustomers as $customer) {

            $isUserAlreadyExists = UserEloquentModel::where('quick_book_user_id',$customer->Id)->first();


            if(!$isUserAlreadyExists){

                $user = UserEloquentModel::create([
                    'name_prefix' => "Mr",
                    'first_name' => $customer->FullyQualifiedName,
                    'last_name' => $customer->FamilyName,
                    'contact_no' => $customer->PrimaryPhone->FreeFormNumber ?? "09",
                    'is_active' => 1,
                    'quick_book_user_id' => $customer->Id,
                ]);

                $user->roles()->sync($roleIds);

                CustomerEloquentModel::create([
                    'status' => 1,
                    'user_id' => $user->id
                ]);
            }
        }

        return true;
    }
}
