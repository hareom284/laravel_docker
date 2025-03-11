<?php

namespace Src\Company\CustomerManagement\Application\Repositories\Eloquent;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;
use Src\Company\StaffManagement\Infrastructure\EloquentModels\StaffEloquentModel;
use Src\Company\CompanyManagement\Domain\Services\QuickbookService;
use Src\Company\CustomerManagement\Application\Mappers\CustomerMapper;
use Src\Company\CustomerManagement\Domain\Repositories\CustomerMobileRepositoryInterface;
use Src\Company\CustomerManagement\Domain\Resources\CustomerResource;
use Src\Company\CustomerManagement\Infrastructure\EloquentModels\CheckListTemplateItemEloquentModel;
use Src\Company\CustomerManagement\Domain\Model\Customer;
use Src\Company\CustomerManagement\Domain\Resources\CustomerMobileResource;
use Src\Company\CustomerManagement\Infrastructure\EloquentModels\CustomerEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\ProjectEloquentModel;

class CustomerMobileRepository implements CustomerMobileRepositoryInterface
{
    private $quickBookService;

    public function __construct(QuickbookService $quickBookService)
    {
        $this->quickBookService = $quickBookService;
    }

    public function findCustomerBySalepersonId($id, $filters = [])
    {

        $perPage = $filters['perPage'] ?? '';

        $statusMapping = [
            1 => 'lead',
            2 => 'customer',
            3 => 'complete',
            4 => 'inactive',
        ];

        $userEloquent = "";

        if (!$id) {
            if (isset($filters['saleperson'])) {
                $staff_info = StaffEloquentModel::query()->where('user_id', $filters['saleperson'])->first();

                $userEloquent = UserEloquentModel::query()->with('customers.assign_staff', 'customer_project.property', 'customers.currentIdMilestone')->with('customers.check_lists')->whereHas('roles', function ($query) {
                    $query->where('role_id', 5);
                })
                    ->whereHas('customers.staffs', function ($query) use ($staff_info) {
                        $query->where('salesperson_uid', $staff_info->id);
                    })
                    // ->where('is_active', true)
                    ->filter($filters)
                    ->orderBy('created_at', 'desc')
                    ->get();
                    // ->paginate($perPage);
            } else {
                $staff_info = StaffEloquentModel::query()->where('user_id', $id)->first();

                $userEloquent = UserEloquentModel::query()->with('customers', 'customers.currentIdMilestone')->with('customers.check_lists')->whereHas('roles', function ($query) {
                    $query->where('role_id', 5);
                })
                    ->filter($filters)
                    ->orderBy('created_at', 'desc')
                    ->get();
                    // ->paginate($perPage);
            }
        } else {

            $staff_info = StaffEloquentModel::query()->where('user_id', $id)->first();

            $userEloquent = UserEloquentModel::query()->with('customers.assign_staff', 'customer_project.property', 'customers.currentIdMilestone')->with('customers.check_lists')->whereHas('roles', function ($query) {
                $query->where('role_id', 5);
            })
                ->filter($filters)
                ->whereHas('customers.staffs', function ($query) use ($staff_info) {
                    $query->where('salesperson_uid', $staff_info->id);
                })
                // ->where('is_active', true)
                ->filter($filters)
                ->orderBy('created_at', 'desc')
                ->get();
                // ->paginate($perPage);
        }

        $user = CustomerMobileResource::collection($userEloquent);

        $userReformat = $user->groupBy(function ($user) use ($statusMapping) {
            $statusNumber = $user->customers->status ?? 'Unknown';
            return $statusMapping[$statusNumber] ?? 'Unknown'; // Group by status
        })->map(function ($group, $status) {
            if ($status === 'customer') {
                // Further group customers by created_at
                return $group->groupBy(function ($customer) {
                    return $customer->created_at 
                        ? $customer->created_at->format('M Y') 
                        : 'Unknown'; // Group by date
                });
            }
            // Return other groups unchanged
            return $group;
        });

        Log::channel('daily')->info($userReformat);

        if(isset($userReformat['customer'])){
            $userReformat['customer']->groupBy('created_at');
        }

        // $links = [
        //     'first' => $user->url(1),
        //     'last' => $user->url($user->lastPage()),
        //     'prev' => $user->previousPageUrl(),
        //     'next' => $user->nextPageUrl(),
        // ];
        // $meta = [
        //     'current_page' => $user->currentPage(),
        //     'from' => $user->firstItem(),
        //     'last_page' => $user->lastPage(),
        //     'path' => $user->url($user->currentPage()),
        //     'per_page' => $perPage,
        //     'to' => $user->lastItem(),
        //     'total' => $user->total(),
        // ];
        $responseData['data'] = $userReformat;
        // $responseData['links'] = $links;
        // $responseData['meta'] = $meta;

        return $responseData;
    }

    public function findCustomerById($id)
    {
        $user_info = UserEloquentModel::query()->with('customers.staffs.user')->findOrFail($id);

        $user = new CustomerMobileResource($user_info);

        return $user;
    }

    public function customerStore(Customer $customer, $salespersonIds)
    {

        $customerEloquent = CustomerMapper::toEloquent($customer);

        $user = $customerEloquent->load('user');

        $salepersonArray = [];

        $checkListItemArray = [];

        foreach ($salespersonIds as $value) {
            $staff_info = StaffEloquentModel::where('user_id', $value)->with('user')->first();

            // comment out this code because no longer needed to send mail to saleperson

            // $salepersonName = $staff_info->user->first_name . ' ' . $staff_info->user->last_name;

            // $customerName = $user->first_name . ' ' . $user->last_name;

            // $salepersonEmail = $staff_info->user->email;

            // $this->salepersonNotifyMail($salepersonName, $salepersonEmail, $customerName);

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

        return $customerEloquent;
    }

    public function customerUpdate($user)
    {

        $user_id = auth('sanctum')->user()->id;

        $salespersonIdsJson = $user['saleperson_ids']; // Access the 'saleperson_ids' JSON string

        // Decode the JSON string into a PHP array
        $salespersonArray = [];
        $salespersonIds = json_decode($salespersonIdsJson, true);
        //map the salesperson id to the staff id as it will be needed for syncing customer to salesperson
        foreach ($salespersonIds as $value) {
            $staff_info = StaffEloquentModel::where('user_id', $value)->with('user')->first();

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


        $customerEloquent = CustomerEloquentModel::query()->where('user_id', $user['user_id'])->first();
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

    public function getCustomerListWithProperties($filters = [])
    {
        //customer lists

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

        $users = $userEloquent->get()->map(function ($user) {
            if (!isset($user->customers)) {
                return [];
            }
        
            $projects = ProjectEloquentModel::get(['property_id']);
            $projects_new = ProjectEloquentModel::with(['customersPivot' => function ($query) {
                $query->select('property_id');
            }])->get();
        
            $propertyIds = $projects_new->pluck('customersPivot')
                ->collapse()
                ->pluck('property_id')
                ->reject(fn($value) => $value === null)
                ->unique()
                ->toArray();
        
            $project_property_id = $projects->pluck('property_id')->toArray();
            $unique_property_ids = array_unique(array_merge($propertyIds, $project_property_id));
        
            return collect($user->customers->customer_properties)
            ->filter(function ($property) use ($unique_property_ids) {
                return !in_array($property->pivot->property_id, $unique_property_ids);
            })
            ->map(function ($property) use ($user, $unique_property_ids) {
                $firstName = $user->first_name ?? null;
                $lastName = $user->last_name ?? null;
                $lead = $firstName && $lastName ? "$firstName $lastName" : ($firstName ?? $lastName ?? '');

                $fullPropertyParts = [
                    $property->street_name ?? null,
                    $property->block_num ?? null,
                    $property->unit_num ? '#' . $property->unit_num : null,
                    $property->postal_code ? 'S(' . $property->postal_code . ')' : null,
                ];
                $fullProperty = collect($fullPropertyParts)
                ->filter()
                ->implode(', ');
        
                return [
                    'id' => $user->id,
                    'lead' => $lead,
                    'customer_id' => $user->customers->id,
                    'property_id' => $property->pivot->property_id,
                    'full_property' => $fullProperty,
                    'street_name' => $property->street_name ?? '',
                    'block_num' => $property->block_num ?? '',
                    'unit_num' => $property->unit_num ?? '',
                    'postal_code' => $property->postal_code ?? '',
                    'isDisabled' => in_array($property->pivot->property_id, $unique_property_ids),
                ];
            })->toArray();
        })->collapse()->toArray();   
        
        return $users;
    }

}
