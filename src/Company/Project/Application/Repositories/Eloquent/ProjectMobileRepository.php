<?php

namespace Src\Company\Project\Application\Repositories\Eloquent;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Src\Company\Document\Infrastructure\EloquentModels\RenovationDocumentsEloquentModel;
use Src\Company\Project\Application\Mappers\ProjectMapper;
use Src\Company\Project\Application\Mappers\PropertyMapper;
use Src\Company\Project\Domain\Mail\ProjectAssignNotiCustomerMail;
use Src\Company\Project\Domain\Model\Entities\Project;
use Src\Company\Project\Domain\Resources\ProjectForAccoutantResource;
use Src\Company\Project\Domain\Resources\ProjectResource;
use Src\Company\Project\Infrastructure\EloquentModels\ProjectEloquentModel;
use Src\Company\Project\Domain\Repositories\ProjectMobileRepositoryInterface;
use Src\Company\Project\Domain\Resources\CustomerPaymentResource;
use Src\Company\Project\Domain\Resources\ProjectDetailForHandoverMobileResource;
use Src\Company\Project\Domain\Resources\ProjectDetailResource;
use Src\Company\Project\Domain\Resources\SupplierCostingResource;
use Src\Company\Project\Infrastructure\EloquentModels\ContactUserEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\PropertyEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\SaleReportEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\SupplierCostingEloquentModel;
use Src\Company\System\Infrastructure\EloquentModels\CompanyEloquentModel;
use Src\Company\System\Infrastructure\EloquentModels\GeneralSettingEloquentModel;

class ProjectMobileRepository implements ProjectMobileRepositoryInterface
{

    public function getProjectListForSaleperson($filters)
    {

        $responseData = null;

        $authUser = auth('sanctum')->user();

        $authId = $authUser->id;

        if ($authUser->roles->contains('name', 'Customer')){
            $projects = ProjectEloquentModel::with('properties')
                ->whereHas('customersPivot', function ($query) use ($authId) {
                    $query->where('user_id', $authId);
                })
                ->filter($filters)
                ->orderBy('created_at','desc')
                ->get();
        } else if ($authUser->roles->contains('name', 'Management')) {
            if ($authUser->roles->contains('name', 'Salesperson')) {
                $projects = ProjectEloquentModel::with('properties')
                    ->whereHas('salespersons', function ($query) use ($authId) {
                        $query->where('salesperson_id', $authId);
                    })
                    ->filter($filters)
                    ->orderBy('created_at','desc')
                    ->get();
            } else {
                $projects = ProjectEloquentModel::with('properties')->get();
            }
        } else if ($authUser->roles->contains('name', 'Vendor')) {
            $projects = ProjectEloquentModel::with('properties')
                ->filter($filters)
                ->orderBy('created_at','desc')
                ->get();
        } else {
            $projects = ProjectEloquentModel::with('properties')
                ->whereHas('salespersons', function ($query) use ($authId) {
                    $query->where('salesperson_id', $authId);
                })
                ->filter($filters)
                ->orderBy('created_at','desc')
                ->get();
        }

        $responseData = ProjectResource::collection($projects)->groupBy('project_status');

        return $responseData;
    }

    public function show(int $id): Object
    {

        $projectEloquent = ProjectEloquentModel::query()
            ->with([
                'salespersons.staffs.rank',
                'salespersons.roles',
                'properties',
                'company',
                'customers.customers',
                'contract',
                'projectRequirements',
                'designWorks',
                'hdbForms',
                'taxInvoices',
                'purchaseOrders',
                'contactUser',
                'projectPortFolios',
                'purchaseOrders',
                'renovation_documents' => function ($query) {
                    $isCustomer = auth()->user() && auth()->user()->roles?->pluck('name')?->contains('Customer');
                    if ($isCustomer) {
                        $query->where('customer_signature','<>','');
                    }
                },
            ])
            ->findOrFail($id);





        $project = new ProjectDetailResource($projectEloquent);

        return $project;
    }

    public function store(Project $project, $request)
    {
        $projectEloquent = ProjectMapper::toEloquent($project);
        $customerIds = json_decode($request->customer_id);
        $salespersonIds = json_decode($request->salesperson_ids);

        PropertyEloquentModel::where('id', $request->property_id)->update([
            'street_name' => $request->street_name,
            'block_num' => $request->block_num,
            'unit_num' => $request->unit_num,
            'postal_code' => $request->postal_code,
            'type_id' => $request->type
        ]);

        $projectEloquent->project_status = 'New';
        $projectEloquent->property_id = $request->property_id;
        $projectEloquent->customer_id = $customerIds[0]->id;
        $projectEloquent->created_by = auth('sanctum')->user()->id;
        $projectEloquent->save();

        if ($request->user_id && $request->user_id != 'null') {
            $projectEloquent->contactUser()->create([
                'user_id' => $request->user_id,
            ]);
        }

        $projectId = $projectEloquent->id;

        $companyName = CompanyEloquentModel::findOrFail($projectEloquent->company_id, ['docu_prefix', 'quotation_no']);

        $initialCompanyName = $companyName->docu_prefix;
        $initialProjectNum = $companyName->quotation_no;

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

        if ($checkCommonProjectNumSetting) {
            $commonPjNum = GeneralSettingEloquentModel::where('setting', 'common_project_start_number')->first();
            $common_project_running_number = $commonPjNum->value;
        }

        if ($checkCommonQuotationNumSetting) {
            $commonQONum = GeneralSettingEloquentModel::where('setting', 'common_quotation_start_number')->first();
            $common_quotation_running_number = $commonQONum->value;
        }

        $initialSalespersonName = $initials;
        $block_num = $request->input('block_num');
        $block_num = str_replace(array('Blk', 'Blk ', 'Block', 'Block ', 'blk', 'blk ', 'BLK', 'BLK ', 'BLOCK', 'BLOCK '), '', $block_num);
        $agreementNum = generateAgreementNumber('project', [
            'company_initial' => $initialCompanyName,
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

        $projectEloquent->agreement_no = $agreementNum;
        $projectEloquent->save();

        foreach ($customerIds as $customer) {
            $projectEloquent->customersPivot()->attach($customer->id, ['property_id' => $customer->property_id]);
        }

        foreach ($salespersonIds as $id) {
            $projectEloquent->salespersons()->attach($id);
        }
        return $projectEloquent;
    }

    public function update(Project $project, $request, $id)
    {
        $projectEloquent = ProjectMapper::toEloquent($project);
        $salespersonIds = $request->salesperson_ids;
        $customerIds = $request->customer_ids;

        if ($request->user_id && $request->user_id != 'null') {
            ContactUserEloquentModel::updateOrCreate(
                ['project_id' => $project->id],
                ['user_id' => $request->user_id]
            );
        } else {
            $contact_user = ContactUserEloquentModel::where('project_id', $project->id)->first();
            if ($contact_user) {
                $contact_user->delete();
            }
        }

        $projectEloquent->agreement_no = $request->agreement_no;
        // need for old data when retrieve
        if (count($customerIds) > 0) {
            $projectEloquent->customer_id = $customerIds[0]['id'];
            $projectEloquent->property_id = $customerIds[0]['property_id'];
        }

        // this is to prevent update project from removing project's property_id
        if(!isset($projectEloquent->property_id) && isset($project->property_id))
            $projectEloquent->property_id = $project->property_id;

        $projectEloquent->save();

        $projectEloquent->salespersons()->sync($salespersonIds);
        $exists_customer_ids = $projectEloquent->customersPivot()->pluck('user_id');
        $projectEloquent->customersPivot()->detach($exists_customer_ids);
        foreach ($customerIds as $customer) {
            $projectEloquent->customersPivot()->attach($customer['id'], ['property_id' => $customer['property_id']]);
        }

        return ProjectMapper::fromEloquent($projectEloquent);
    }


    public function sendMailToCustomer(int $projectId, $customerName, $customerEmail, $customerPassword)
    {
        $project = ProjectEloquentModel::find($projectId);

        $companyName = $project->company->name;

        $user = auth('sanctum')->user();

        $salespersonName = $user->first_name . $user->last_name;

        Mail::to($customerEmail)->send(new ProjectAssignNotiCustomerMail($customerEmail, $customerName, $customerPassword, $companyName, $salespersonName));

        return true;
    }

    public function getCompanyStampByProjectId(int $projectId)
    {
        $projectEloquent = ProjectEloquentModel::find($projectId);

        $company = CompanyEloquentModel::find($projectEloquent->company_id);

        //$stampUrl = $company->company_stamp ? asset('storage/stamp/' . $company->company_stamp) : "-";

        $stampUrl = $company->company_stamp ? base64_encode(file_get_contents(storage_path('app/public/stamp/' . $company->company_stamp))) : "-";

        return $stampUrl;
    }

    public function projectDetailForHandover(int $projectId)
    {
        $projectEloquent = ProjectEloquentModel::query()
            ->with([
                'saleReport.customer_payments',
                'company',
                'property',
                'renovation_documents' => function ($query) {
                    $query->whereNotNull('signed_date');
                }
            ])
            ->findOrFail($projectId);

        $projectDetail = new ProjectDetailForHandoverMobileResource($projectEloquent);

        return $projectDetail;
    }

    public function getProjectReport(int $projectId)
    {
        // Fetch the sale report with customer payments
        $saleReports = SaleReportEloquentModel::query()
            ->with('customer_payments')
            ->where('project_id', $projectId)
            ->first();

        // Fetch supplier costings
        $supplierCostings = SupplierCostingEloquentModel::where('project_id', $projectId)->get();

        $cost = 0;
        $showCostWithoutRebate = GeneralSettingEloquentModel::where('setting','show_project_cost_without_rebate_to_designer')->where('value', 'true')->first();

        foreach ($supplierCostings as $supplierCosting) {
            if(isset($showCostWithoutRebate))
                $cost += $supplierCosting->payment_amt;
            else
                $cost += ($supplierCosting->payment_amt - $supplierCosting->discount_amt);
        }

        // Initialize payment, cost, and profit values
        $payment = isset($saleReports->paid) ? $saleReports->paid : 0;
        $totalSales = isset($saleReports->total_sales) ? $saleReports->total_sales : 0;
        $netProfit = isset($saleReports->total_sales) ? $saleReports->total_sales - $cost : 0;
        $profitMargin = isset($saleReports->total_sales) && $cost > 0 ? ( ($saleReports->total_sales - $cost) / $saleReports->total_sales) * 100 : 0;

        // Calculate payment percentage
        $paymentPercentage = $totalSales > 0 ? ($payment / $totalSales) * 100 : 0;

        $profitAndLoss = $this->getProfileAndList($projectId);

        // Prepare the response data
        $data = [
            "payment" => number_format($payment, 2, '.', ','),
            "cost" => number_format($cost, 2, '.', ','),
            "sales" =>  number_format($totalSales, 2, '.', ','),
            "profit" => number_format($netProfit, 2, '.', ','),
            "profit_margin" => round($profitMargin),
            "payment_percentage" => round($paymentPercentage, 2), // Rounded to 2 decimal places
            "supplier_cost" => SupplierCostingResource::collection($supplierCostings),
            "payments" => CustomerPaymentResource::collection($saleReports->customer_payments ?? []),
            "profit_and_loss" => $profitAndLoss
        ];

        return $data;

    }

    public function getProjectsForManagement($perPage, $salePerson,$filterText,$status,$created_at,$cardView)
    {
        $projectsQuery = ProjectEloquentModel::with('properties', 'customers', 'salespersons', 'renovation_documents', 'saleReport.customer_payments.paymentType')->orderBy('project_status', 'DESC');

        if ($salePerson !== 0) {
            $projectsQuery->whereHas('salespersons', function ($query) use ($salePerson) {
                $query->where('salesperson_id', $salePerson);
            });
        }

        if (!empty($filterText)) {
            $words = explode(' ', trim($filterText)); // Splitting the input text by spaces

            $projectsQuery->where(function ($query) use ($words) {
                $query->whereHas('properties', function ($query) use ($words) {
                    $query->where(function ($query) use ($words) {
                        foreach ($words as $word) {
                            $query->orWhere('street_name', 'LIKE', "%{$word}%")
                                  ->orWhere('block_num', 'LIKE', "%{$word}%")
                                  ->orWhere('unit_num', 'LIKE', "%{$word}%")
                                  ->orWhere('postal_code', 'LIKE', "%{$word}%");
                        }
                    });
                })
                ->orWhereHas('customers', function ($query) use ($words) {
                    $query->where(function ($query) use ($words) {
                        foreach ($words as $word) {
                            $query->orWhere('first_name', 'LIKE', "%{$word}%")
                                  ->orWhere('last_name', 'LIKE', "%{$word}%");
                        }
                    });
                })
                ->orWhereHas('salespersons', function ($query) use ($words) {
                    $query->where(function ($query) use ($words) {
                        foreach ($words as $word) {
                            $query->orWhere('first_name', 'LIKE', "%{$word}%")
                                  ->orWhere('last_name', 'LIKE', "%{$word}%");
                        }
                    });
                });
            });
        }

        if(!empty($status)){
            $projectsQuery->where('project_status', $status);
        }

        if(!empty($created_at)){
            if($created_at == 'desc'){
                $projectsQuery->orderBy('created_at', 'desc');
            } else if($created_at == 'asc'){
                $projectsQuery->orderBy('created_at', 'asc');
            }
        }

        $authUser = auth('sanctum')->user();

        if ($authUser->roles->contains('name', 'Manager')) {
            $staff = $authUser->staffs;
            if ($staff) {
                $projectsQuery->whereHas('salespersons', function ($query) use ($staff) {
                    $query->whereIn('users.id', function($subQuery) use ($staff) {
                        $subQuery->select('user_id')
                                  ->from('staffs')
                                  ->where('mgr_id', $staff->user_id);
                    });
                });
            }
        }

        $projects = $projectsQuery
        ->orderByRaw("CASE WHEN payment_status = 'REQUEST' THEN 1 WHEN payment_status = 'APPROVED' THEN 2 ELSE 3 END")
        ->orderBy('created_at','desc')
        ->get();

        if($cardView)
        {

            $statusMapping = getCurrentProjectStatusMap();

            $finalResults = ProjectResource::collection($projects)
            ->filter(function ($project) use ($statusMapping) {
                return isset($statusMapping[$project->project_status]);
            })
            ->groupBy(function ($project) use ($statusMapping) {
                return $statusMapping[$project->project_status];
            });

        } else {
            $finalResults = ProjectResource::collection($projects);
        }

        return $finalResults;
    }

    private function getProfileAndList($projectId) {
        $quotation = RenovationDocumentsEloquentModel::where('project_id', $projectId)
                    ->where('type','QUOTATION')
                    ->whereNotNull('signed_date')
                    ->first();

        $data = [];

        if(isset($quotation)) {
            foreach($quotation->renovation_sections as $renovation_section) {
                $vendors = DB::table('section_vendor')
                ->where('section_id', $renovation_section->sections->id)
                ->pluck('vendor_id')
                ->toArray();

                $vendor_cost = 0;

                if(!empty($vendors)) {
                    $vendor_cost = SupplierCostingEloquentModel::where('project_id', $projectId)
                                    ->whereIn('vendor_id', $vendors)
                                    ->selectRaw('SUM(payment_amt - discount_amt) as total_amount')
                                    ->value('total_amount');
                }

                $quoted_profit_amount = $quoted_profit_percent = 0;
                if($renovation_section->total_price > 0) {
                    $quoted_profit_amount = $renovation_section->total_price - $renovation_section->total_cost_price;

                    if($renovation_section->total_cost_price > 0)
                        $quoted_profit_percent = round(($quoted_profit_amount / $renovation_section->total_price) * 100, 2);
                }

                $data[] = [
                    "section_name" => $renovation_section->name,
                    "signed_amount" => round($renovation_section->total_price, 2),
                    "vendor_cost" => round($vendor_cost, 0),
                    "profit_amount" => round($renovation_section->total_price - $vendor_cost, 2),
                    "profit_percent" => $renovation_section->total_price > 0 ? round(($renovation_section->total_price - $vendor_cost) / $renovation_section->total_price * 100, 2) : 0,
                    "quoted_cost" => round($renovation_section->total_cost_price, 2),
                    "quoted_profit_amount" => $quoted_profit_amount,
                    "quoted_profit_percent" => $quoted_profit_percent
                ];
            }

        }

        return $data;
    }
}
