<?php

namespace Src\Company\CustomerManagement\Application\Repositories\Eloquent;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;
use Src\Company\UserManagement\Domain\Resources\UserResource;
use Src\Company\StaffManagement\Infrastructure\EloquentModels\StaffEloquentModel;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Src\Company\CompanyManagement\Domain\Services\QuickbookService;
use Src\Company\CustomerManagement\Application\Mappers\CustomerMapper;
use Src\Company\CustomerManagement\Domain\Model\Customer;
use Src\Company\CustomerManagement\Domain\Mails\LeadCreateSuccessMail;
use Src\Company\CustomerManagement\Domain\Mails\NotifySalepersonMail;
use Src\Company\CustomerManagement\Domain\Repositories\CustomerRepositoryInterface;
use Src\Company\CustomerManagement\Domain\Resources\CustomerResource;
use Src\Company\CustomerManagement\Domain\Resources\CustomerWithEmailResource;
use Src\Company\CustomerManagement\Domain\Resources\GroupCustomerResources;
use Src\Company\CustomerManagement\Domain\Resources\ManagerLeadManagementResources;
use Src\Company\CustomerManagement\Domain\Resources\SalepersonLeadManagementResource;
use Src\Company\CustomerManagement\Infrastructure\EloquentModels\CustomerEloquentModel;
use Src\Company\CustomerManagement\Infrastructure\EloquentModels\CheckListTemplateItemEloquentModel;
use Src\Company\CustomerManagement\Infrastructure\EloquentModels\IdMilestonesEloquentModel;

class CustomerRepository implements CustomerRepositoryInterface
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

    public function customerStore(Customer $customer, $salespersonIds)
    {

        $customerEloquent = CustomerMapper::toEloquent($customer);

        $user = $customerEloquent->user;

        $salepersonArray = [];

        $checkListItemArray = [];

        foreach ($salespersonIds as $value) {
            $staff_info = StaffEloquentModel::where('user_id', $value)->with('user')->first();
            if ($value != auth()->id()) {
                $salepersonName = $staff_info->user->first_name . ' ' . $staff_info->user->last_name;
                $customerName = $user->first_name . ' ' . $user->last_name;
                $salepersonEmail = $staff_info->user->email;
        
                // Send email to the salesperson
                $this->salepersonNotifyMail($salepersonName, $salepersonEmail, $customerName);
            }

            array_push($salepersonArray, $staff_info->id);
        }

        $checkListItemEloquent = CheckListTemplateItemEloquentModel::all();

        foreach ($checkListItemEloquent as $checkListItem) {
            array_push($checkListItemArray, $checkListItem->id);
        }

        $customerEloquent->save();
        $customerEloquent->staffs()->sync($salepersonArray);

        $customerEloquent->leadCheckLists()->attach($checkListItemArray);

        // $customerEloquent->idMilestones()->attach($customerEloquent->id_milestone_id);

        /*
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

                $user->quick_book_user_id = $qboRecentCusomter->Id;

                $user->save();
            } else {

                $user->quick_book_user_id = $quickBookCustomer->Id;

                $user->save();
            }
        }
        */

        return $customerEloquent;
    }



    public function customerUpdate($user)
    {

        $user_id = auth('sanctum')->user()->id;

        $salespersonIdsJson = $user['saleperson_ids']; // Access the 'saleperson_ids' JSON string

        // Decode the JSON string into a PHP array
        $salespersonArray = [];
        $salespersonIds = json_decode($salespersonIdsJson, true);
        $customerEloquent = CustomerEloquentModel::query()->with('currentIdMilestone')->where('user_id', $user['user_id'])->first();

        $existingSalespersonIds = $customerEloquent->staffs()->pluck('user_id')->toArray();
        //map the salesperson id to the staff id as it will be needed for syncing customer to salesperson
        foreach ($salespersonIds as $value) {
            $staff_info = StaffEloquentModel::where('user_id', $value)->with('user')->first();

            if (!in_array($value, $existingSalespersonIds) && $value != auth()->id()) {
                $salepersonName = $staff_info->user->first_name . ' ' . $staff_info->user->last_name;
                $customerName = $customerEloquent->user->first_name . ' ' . $customerEloquent->user->last_name;
                $salepersonEmail = $staff_info->user->email;
        
                // Send email to the newly assigned salesperson
                $this->salepersonNotifyMail($salepersonName, $salepersonEmail, $customerName);
            }

            array_push($salespersonArray, $staff_info->id);
        }

        if (isset($user['customer_attachment']) && $user['customer_attachment'] instanceof \Illuminate\Http\UploadedFile) {
            $customerAttachment = time() . '.' . $user['customer_attachment']->extension();

            $customerAttachmentPath = 'customer_attachment/' . $customerAttachment;

            Storage::disk('public')->put($customerAttachmentPath, file_get_contents($user['customer_attachment']));

            $customerAttachmentFile = $customerAttachment;

            if (Storage::disk('public')->exists('customer_attachment/'.$user['original_customer_attachment'])) {
                Storage::disk('public')->delete('customer_attachment/'.$user['original_customer_attachment']);
            }

        } else {
            $customerAttachmentFile = isset($user['original_customer_attachment']) ? $user['original_customer_attachment'] : null;
        }


        // sync the salesperson(s) to customer
        $customerEloquent->staffs()->sync($salespersonArray);

        $customerEloquent->idMilestones()->updateExistingPivot($user['id_milestone_id'], [
            'id_milestone_id' => $user['id_milestone_id']
        ]);

        $customerEloquent->update([
            'nric' => $user['nric'] ?? null,
            'attachment' => $customerAttachmentFile,
            'source' => $user['source'] ?? null,
            'additional_information' => $user['additional_information'] ?? null,
            'last_modified_by' => $user_id,
            'company_name' => $user['company_name'] ?? null,
            'customer_type' => $user['customer_type'] ?? null,
            'budget' => $user['budget'],
            'quote_value' => $user['quote_value'] ?? null,
            'book_value' => $user['book_value'] ?? null,
            'key_collection' => $user['key_collection'],
            'id_milestone_id' => $user['id_milestone_id'],
            'rejected_reason_id' => $user['rejected_reason_id'] ?? null,
            'next_meeting' => $user['next_meeting'],
            'days_aging' => $user['days_aging'] ?? null,
            'remarks' => $user['remarks'] ?? null,
            'budget_value' => $user['budget_value'] ?? null,
        ]);

        $qboConfig = config('quickbooks');

        if ($qboConfig['qbo_integration']) {

            $userEloquent = UserEloquentModel::query()->where('id', $user['user_id'])->first();

            $properties = json_decode($user['properties'], true);

            $cusAddress = $properties[0]['block_num'] . ' ' . $properties[0]['street_name'] . ' #' . $properties[0]['unit_num'] ;
            $cusPostalCode = $properties[0]['postal_code'];

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

                $customerData = [
                    'name' => $customerName,
                    'companyName' => ($type === 1) ? $customerName : null,
                    'email' => $customerEmail,
                    'address' => $cusAddress,
                    'postal_code' => $cusPostalCode,
                    'contact_no' => $customerNo
                ];

                $this->quickBookService->updateCustomer($userEloquent->quick_book_user_id,$customerData);
            }
        }

        return $customerEloquent;
    }


    public function inactive(int $user_id)
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
        return $customer;
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
        return $customer;
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

    public function getLeadManagementReport($data = [])
    {

        $status = $data['filters']['status'];
        $leadSource = $data['filters']['lead_source'];
        $startDate = $data['filters']['start_date'];
        $endDate = $data['filters']['end_date'];

        if (isset($data['saleperson_id'])) {
            $staff_info = StaffEloquentModel::query()->with('mgr', 'user')->where('user_id', $data['saleperson_id'])->first();

            $customerEloquent = CustomerEloquentModel::with('user', 'currentIdMilestone', 'rejectedReason')->whereHas('staffs', function ($query) use ($staff_info) {
                $query->where('salesperson_uid', $staff_info->id);
            })
                ->when(isset($startDate) && isset($endDate), function ($query) use ($startDate, $endDate) {
                    $query->whereHas('user', function ($query) use ($startDate, $endDate) {
                        $query->whereBetween('created_at', [
                            $startDate . ' 00:00:00', 
                            $endDate . ' 23:59:59'
                        ]);
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

            $customerEloquent = CustomerEloquentModel::with('user', 'currentIdMilestone', 'rejectedReason')->whereHas('staffs', function ($query) use ($staffIds) {
                $query->whereIn('salesperson_uid', $staffIds);
            })
                ->when(isset($startDate) && isset($endDate), function ($query) use ($startDate, $endDate) {
                    $query->whereHas('user', function ($query) use ($startDate, $endDate) {
                        $query->whereBetween('created_at', [
                            $startDate . ' 00:00:00', 
                            $endDate . ' 23:59:59'
                        ]);
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
            $customerEloquent = CustomerEloquentModel::with('user', 'staffs.mgr', 'currentIdMilestone', 'rejectedReason')->whereHas('staffs')
                ->when(isset($startDate) && isset($endDate), function ($query) use ($startDate, $endDate) {
                    $query->whereHas('user', function ($query) use ($startDate, $endDate) {
                        $query->whereBetween('created_at', [
                            $startDate . ' 00:00:00', 
                            $endDate . ' 23:59:59'
                        ]);
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


        $userEloquent = UserEloquentModel::query()->with('customers', 'projectPivot.customersPivot')->whereHas('roles', function ($query) {
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
            if (count($user->projectPivot) > 0) { // temporary solution to fix Lead List Page issue
                if (count($user->projectPivot[0]->customersPivot) > 1) {
                    $userId = $user->projectPivot[0]->customersPivot[0]->id;

                    if ($user->id !== $userId) {
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

    public function getGroupSalepersonLeadManagementList($mgr_id, $filters = [])
    {

        $perPage = $filters['perPage'] ?? 10;
        $status = $filters['status'];
        $leadSource = $filters['lead_source'];
        $startDate = $filters['start_date'];
        $endDate = $filters['end_date'];
        $name = $filters['name'];

        $userEloquent = CustomerEloquentModel::with('user.projectPivot.customersPivot', 'staffs.mgr', 'staffs.user')
            ->when(isset($startDate) && isset($endDate), function ($query) use ($startDate, $endDate) {
                $query->whereHas('user', function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [
                            $startDate . ' 00:00:00', 
                            $endDate . ' 23:59:59'
                    ]);
                });
            })
            ->when($name ?? false, function($query, $name){
                $query->whereHas('user', function ($query) use ($name) {
                    $query->where('first_name', 'like', '%' . $name . '%')
                    ->orWhere('last_name', 'like', '%' . $name . '%');
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
            if (count($customer->user->projectPivot) > 0) {
                if (count($customer->user->projectPivot[0]->customersPivot) > 1) {
                    $userId = $customer->user->projectPivot[0]->customersPivot[0]->id;

                    if ($customer->user->id !== $userId) {
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
                    'id' => isset($sale['id']) ? $sale['id'] : '',
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

        $userEloquent = UserEloquentModel::query()->with('customers', 'projectPivot.customersPivot')->whereHas('roles', function ($query) {
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
            if (count($user->projectPivot) > 0) { // temporary solution to fix Lead List Page issue
                if (count($user->projectPivot[0]->customersPivot) > 1) {
                    $userId = $user->projectPivot[0]->customersPivot[0]->id;

                    if ($user->id !== $userId) {
                        $user->projectPivot[0]->customersPivot = [];
                    }
                }

                $totalCxCount += count($user->projectPivot[0]->customersPivot);
            }
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

            $idMilestone = IdMilestonesEloquentModel::query()->where('name', $data['id_milestone'])->first();

            if (!$idMilestone) {
                $idMilestone = IdMilestonesEloquentModel::create([
                    'name' => $data['id_milestone']
                ]);
            }

            $customerEloquent = CustomerEloquentModel::query()->where('user_id', $user_id)->first();
            $customerEloquent->update([
                'id_milestone_id' => $idMilestone->id
            ]);

            $customerEloquent->idMilestones()->updateExistingPivot($idMilestone->id, [
                'id_milestone_id' => $idMilestone->id
            ]);
        }

        return "Success";
    }

    public function syncLeadWithQuickbook()
    {
        $qboCustomers = $this->quickBookService->getAllCustomers();

        $roleIds = ['5'];

        foreach ($qboCustomers as $customer) {

            $isUserAlreadyExists = UserEloquentModel::where('quick_book_user_id', $customer->Id)->first();


            if (!$isUserAlreadyExists) {

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

    public function sendSuccessMail($name, $email, $password, $siteSetting, $salespersonNames)
    {
        $user = auth('sanctum')->user();

        $salespersonName = $user->first_name . $user->last_name;

        Mail::to($email)->send(new LeadCreateSuccessMail($name, $email, $password, $siteSetting, $salespersonNames));

        return true;
    }

    public function getUsersToNotify($roles, $customer_id)
    {
        $usersToNotify = collect();
        if (in_array("Salesperson", $roles)) {
            $usersToNotify = $usersToNotify->merge($this->getUsersRelatedToSalesperson($customer_id));
        }

        if (in_array("Manager", $roles)) {
            $usersToNotify = $usersToNotify->merge($this->getUsersRelatedToManager($customer_id));
        }

        $usersToNotify = $usersToNotify->unique('id');
        return $usersToNotify;
    }

    public function getUsersRelatedToSalesperson($customer_id)
    {

        $customer = CustomerEloquentModel::where('id', $customer_id)->with('staffs.user')->first();

        $users = [];

        foreach ($customer->staffs as $staff) {
            array_push($users, $staff->user_id);
        }

        return $users;
    }

    public function getUsersRelatedToManager($customer_id)
    {

        $customer = CustomerEloquentModel::where('id', $customer_id)->with('staffs.mgr')->first();

        $users = [];

        foreach ($customer->staffs as $staff) {
            array_push($users, $staff->mgr_id);
        }

        return $users;
    }

    public function salepersonNotifyMail($salepersonName, $salepersonEmail, $customerName)
    {
        logger([$salepersonEmail]);
        Mail::to($salepersonEmail)->send(new NotifySalepersonMail($salepersonName, $customerName));

        return true;
    }

}
