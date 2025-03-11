<?php

namespace Src\Company\Project\Application\Repositories\Eloquent;

use Carbon\Carbon;
use Src\Company\Document\Application\Mappers\DocumentMapper;
use Src\Company\Project\Application\Mappers\ProjectMapper;
use Src\Company\Project\Domain\Model\Entities\Project;
use Src\Company\Project\Domain\Repositories\ProjectRepositoryInterface;
use Src\Company\Project\Application\DTO\ProjectData;
use Src\Company\Project\Application\UseCases\Commands\DeletePropertyCommand;
use Src\Company\Project\Domain\Resources\ProjectDetailResource;
use Src\Company\Project\Domain\Resources\ProjectForAccoutantResource;
use Src\Company\Project\Domain\Resources\ProjectResource;
use Src\Company\Project\Infrastructure\EloquentModels\ProjectEloquentModel;
use Src\Company\System\Infrastructure\EloquentModels\CompanyEloquentModel;
use Src\Company\Project\Domain\Mail\ProjectAssignNotiCustomerMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Src\Company\Project\Domain\Resources\NewProjectResource;
use Src\Company\Project\Domain\Resources\ProjectDetailForHandoverResource;
use Src\Company\System\Infrastructure\EloquentModels\GeneralSettingEloquentModel;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;

class ProjectRepository implements ProjectRepositoryInterface
{
    public function getProjects()
    {

        $authUser = auth('sanctum')->user();

        $authId = $authUser->id;

        if ($authUser->roles->contains('name', 'Management')) {
            if ($authUser->roles->contains('name', 'Salesperson')) {
                $projects = ProjectEloquentModel::with('properties')
                    ->whereHas('salespersons', function ($query) use ($authId) {
                        $query->where('salesperson_id', $authId);
                    })
                    ->get();
            } else {
                $projects = ProjectEloquentModel::with('properties')->get();
            }
        } else {
            $projects = ProjectEloquentModel::with('properties')
                ->whereHas('salespersons', function ($query) use ($authId) {
                    $query->where('salesperson_id', $authId);
                })
                ->get();
        }

        // return response()->error($authUser->roles->contains('name', 'Management'),"Duplicate Property Address",422);

        $final_result = ProjectResource::collection($projects)->groupBy('project_status');

        return $final_result;
    }

    public function getProjectLists()
    {
        $projects = ProjectEloquentModel::with('properties', 'customersPivot')->get();

        $final_result = ProjectResource::collection($projects);

        return $final_result;
    }

    public function projectByCustomerId(int $customerId)
    {
        $projects = ProjectEloquentModel::with('properties','salespersons')->whereHas('customersPivot', function ($query) use ($customerId) {
            $query->where('user_id', $customerId);
        })->where('project_status', '!=', 'Cancelled')->get();

        $final_result = ProjectResource::collection($projects);

        return $final_result;
    }

    public function getProjectsForAccountant($perPage, $salePerson,$filterText, $status, $created_at)
    {
        $projectsQuery = ProjectEloquentModel::with('properties', 'customers', 'salespersons', 'renovation_documents', 'saleReport.customer_payments.paymentType')
            ->orderBy('project_status', 'DESC')
            ->where('project_status', '!=', 'New');
        $statusMapping = getCurrentProjectStatusMap();

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
        $allowedStatuses = array_keys($statusMapping);
        $projects = $projectsQuery
        ->whereIn('project_status', $allowedStatuses)
        ->orderByRaw("CASE WHEN payment_status = 'REQUEST' THEN 1 WHEN payment_status = 'APPROVED' THEN 2 ELSE 3 END")
        ->orderBy('created_at','desc')
        ->paginate($perPage);

        $finalResults = ProjectForAccoutantResource::collection($projects);

        $links = [
            'first' => $projects->url(1),
            'last' => $projects->url($projects->lastPage()),
            'prev' => $projects->previousPageUrl(),
            'next' => $projects->nextPageUrl(),
        ];

        $meta = [
            'current_page' => $projects->currentPage(),
            'from' => $projects->firstItem(),
            'last_page' => $projects->lastPage(),
            'path' => $projects->url($projects->currentPage()),
            'per_page' => $perPage,
            'to' => $projects->lastItem(),
            'total' => $projects->total(),
        ];

        $responseData['data'] = $finalResults;
        $responseData['links'] = $links;
        $responseData['meta'] = $meta;

        return $responseData;
    }

    public function getInProgressProjects()
    {
        $projects = ProjectEloquentModel::with('properties', 'customers', 'salespersons')->where('project_status', 'InProgress')->get();

        $finalResults = ProjectForAccoutantResource::collection($projects);

        return $finalResults;
    }

    public function getProjectListsForOthers($filters)
    {
        $responseData = null;

        if(isset($filters['customer_id'])){
            $projects = ProjectEloquentModel::with('properties')->whereHas('customersPivot', function ($query) use ($filters) {
                $query->where('user_id', $filters['customer_id']);
            })->where('project_status', '!=', 'Cancelled')->get();

            $responseData = ProjectResource::collection($projects)->groupBy('project_status');
        } else {
            $projects = ProjectEloquentModel::with('properties')->get();

            $responseData = ProjectResource::collection($projects)->groupBy('project_status');
        }

        return $responseData;

    }

    public function getProjectListForSaleperson($filters)
    {

        $responseData = null;
        $statusMapping = getCurrentProjectStatusMap();

        if ($filters['cardView'] == 'true') {
            $authUser = auth('sanctum')->user();

            $authId = $authUser->id;

            if ($authUser->roles->contains('name', 'Management')) {
                if ($authUser->roles->contains('name', 'Salesperson')) {
                    $projects = ProjectEloquentModel::with('properties')
                        ->whereHas('salespersons', function ($query) use ($authId) {
                            $query->where('salesperson_id', $authId);
                        })
                        ->filter($filters)
                        ->get();
                } else {
                    $projects = ProjectEloquentModel::with('properties')->get();
                }
            } else {
                $projects = ProjectEloquentModel::with('properties')
                    ->whereHas('salespersons', function ($query) use ($authId) {
                        $query->where('salesperson_id', $authId);
                    })
                    ->filter($filters)
                    ->get();
            }

            // return response()->error($authUser->roles->contains('name', 'Management'),"Duplicate Property Address",422);

            // $responseData = ProjectResource::collection($projects)->groupBy('project_status');
            // $responseData = ProjectResource::collection($projects)
            // ->groupBy(function ($project) use ($statusMapping) {
            //     return $statusMapping[$project->project_status];
            // });

            $responseData = ProjectResource::collection($projects)
            ->filter(function ($project) use ($statusMapping) {
                return isset($statusMapping[$project->project_status]);
            })
            ->groupBy(function ($project) use ($statusMapping) {
                return $statusMapping[$project->project_status];
            });
        } else if ($filters['cardView'] == 'false') {
            $projectsQuery = ProjectEloquentModel::with('properties', 'customers', 'salespersons', 'renovation_documents', 'saleReport.customer_payments.paymentType')->orderBy('project_status', 'DESC')->filter($filters);

            $salePerson = $filters['salePerson'];

            if ($salePerson !== 0) {
                $projectsQuery->whereHas('salespersons', function ($query) use ($salePerson) {
                    $query->where('salesperson_id', $salePerson);
                });
            }
            $allowedStatuses = array_keys($statusMapping);
            $projects = $projectsQuery->whereIn('project_status', $allowedStatuses)->paginate($filters['perPage']);

            $finalResults = ProjectForAccoutantResource::collection($projects);

            $links = [
                'first' => $projects->url(1),
                'last' => $projects->url($projects->lastPage()),
                'prev' => $projects->previousPageUrl(),
                'next' => $projects->nextPageUrl(),
            ];

            $meta = [
                'current_page' => $projects->currentPage(),
                'from' => $projects->firstItem(),
                'last_page' => $projects->lastPage(),
                'path' => $projects->url($projects->currentPage()),
                'per_page' => $filters['perPage'],
                'to' => $projects->lastItem(),
                'total' => $projects->total(),
            ];

            $responseData['data'] = $finalResults;
            $responseData['links'] = $links;
            $responseData['meta'] = $meta;
        }

        return $responseData;
    }

    public function getProjectsForManagement($perPage, $salePerson,$filterText,$status,$created_at)
    {
        $projectsQuery = ProjectEloquentModel::with('properties', 'customers', 'salespersons', 'renovation_documents', 'saleReport.customer_payments.paymentType')->orderBy('project_status', 'DESC');
        $statusMapping = getCurrentProjectStatusMap();

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
        $allowedStatuses = array_keys($statusMapping);
        $projects = $projectsQuery
        ->whereIn('project_status', $allowedStatuses)
        ->orderByRaw("CASE WHEN payment_status = 'REQUEST' THEN 1 WHEN payment_status = 'APPROVED' THEN 2 ELSE 3 END")
        ->orderBy('created_at','desc')
        ->paginate($perPage);

        $finalResults = ProjectForAccoutantResource::collection($projects);

        $links = [
            'first' => $projects->url(1),
            'last' => $projects->url($projects->lastPage()),
            'prev' => $projects->previousPageUrl(),
            'next' => $projects->nextPageUrl(),
        ];

        $meta = [
            'current_page' => $projects->currentPage(),
            'from' => $projects->firstItem(),
            'last_page' => $projects->lastPage(),
            'path' => $projects->url($projects->currentPage()),
            'per_page' => $perPage,
            'to' => $projects->lastItem(),
            'total' => $projects->total(),
        ];

        $responseData['data'] = $finalResults;
        $responseData['links'] = $links;
        $responseData['meta'] = $meta;

        return $responseData;
    }

    public function store(Project $project, $property_id, $salespersonIds, $document, $block_num): ProjectData
    {
        $createdDate = date("my", strtotime(Carbon::now()));

        $companyName = CompanyEloquentModel::findOrFail($project->company_id, ['docu_prefix']);

        // $initialCompanyName = implode("", array_map(fn ($word) => $word[0], explode(" ", $companyName->name)));

        $initialCompanyName = $companyName->docu_prefix;

        $name = auth('sanctum')->user()->first_name . ' ' . auth('sanctum')->user()->last_name;

        $initialSalespersonName = implode("", array_map(fn ($word) => $word[0], explode(" ", $name)));

        $agreement_no = "$initialCompanyName/$initialSalespersonName/$createdDate/$block_num";

        $projectEloquent = ProjectMapper::toEloquent($project, $property_id, $agreement_no);

        $projectEloquent->save();

        foreach ($salespersonIds as $id) {
            $projectEloquent->salespersons()->attach($id);
        }

        if ($document) {
            $documentEloquent = DocumentMapper::toEloquent($document);

            $projectEloquent->documents()->save($documentEloquent);
        }

        return ProjectMapper::fromEloquent($projectEloquent);
    }

    public function update(Project $project, $salespersonIds, $agreement_no, $customerIds, $id)
    {   
        $oldProject = ProjectEloquentModel::find($id);
        $oldCompany = $oldProject->company;
        $salesperson = UserEloquentModel::find($oldProject->created_by);
        $project_no_format_value = GeneralSettingEloquentModel::where('setting', 'project_agr_no_format')
        ->value('value');
        if (empty($project_no_format_value)) {
            $project_no_format_value = false;
        }
        $project_no_format_value = $project_no_format_value ?: '{company_initial}/{salesperson_initial}/{date:my}/{block_num}';
        $agreementDate = isset($oldProject->created_at) ? Carbon::parse($oldProject->created_at)->toDateString() : Carbon::now()->toDateString();

        $name = $salesperson->first_name . ' ' . $salesperson->last_name;
        $words = array_filter(explode(" ", $name));
        $initials = '';

        foreach ($words as $word) {
            if (strlen($word) > 0) {
                $initials .= $word[0];
            }
        }
        $initialSalespersonName = $initials;
        $block_num = request('block_num');
        $block_num = str_replace(array('Blk', 'Blk ', 'Block', 'Block ','blk','blk ','BLK','BLK ','BLOCK','BLOCK '), '', $block_num);
        $date = Carbon::createFromFormat('Y-m-d', $agreementDate ?? '');
        preg_match('/\{date:([a-zA-Z]+)\}/', $project_no_format_value, $matches);
        if(!empty($matches)){
            $dateFormat = $matches[1];
            $formattedDate = $date->format($dateFormat);
        } else {
            $formattedDate = "";
        }

        $placeholders = [
            'quotation_initial' => $oldCompany->quotation_prefix,
            'company_initial' => $oldCompany->docu_prefix,
            'block_num' => $block_num,
            'salesperson_initial' => $initialSalespersonName,
            'date' => $formattedDate
        ];
        $projectEloquent = ProjectMapper::toEloquent($project);
        $projectEloquent->agreement_no = $agreement_no;
        // need for old data when retrieve
        if (count($customerIds) > 0) {
            $projectEloquent->customer_id = $customerIds[0]['id'];
            $projectEloquent->property_id = $customerIds[0]['property_id'];
        }

        // this is to prevent update project from removing project's property_id
        if(!isset($projectEloquent->property_id) && isset($project->property_id))
            $projectEloquent->property_id = $project->property_id;


        $projectEloquent->save();

        $isProjectInprogress = $projectEloquent->renovation_documents->contains(function ($document) {
            return $document->type === 'QUOTATION' && !is_null($document->signed_date);
        });
        if(!$isProjectInprogress){
            $company = $projectEloquent->company;
            $quotationPrefix = $company->quotation_prefix;
            $documentPrefix = $company->docu_prefix;

            $parsedProjectNoData = parseData($projectEloquent->agreement_no, $project_no_format_value, $placeholders);
            $agreementNum = generateAgreementNumber('project',[
                'company_initial' => $documentPrefix,
                'quotation_initial' => $quotationPrefix,
                'salesperson_initial' => $parsedProjectNoData['salesperson_initial'] ?? null,
                'block_num' => $parsedProjectNoData['block_num'] ?? null,
                'date' => $agreementDate,
                'project_id' => $projectEloquent->id, 
                'quotation_num' => $parsedProjectNoData['quotation_num'] ?? null,
                'running_num' => $parsedProjectNoData['running_num'] ?? null,
                'project_agr_no' => null,
                'common_project_running_number' => $parsedProjectNoData['common_project_running_num'] ?? null,
                'common_quotation_running_number' => $parsedProjectNoData['common_quotation_running_num'] ?? null,
            ]);
            $projectEloquent->agreement_no = $agreementNum;
            $projectEloquent->save();

            $projectEloquent->renovation_documents
            ->filter(function ($document) {
                // Filter documents in progress (e.g., QUOTATION type and signed_date is not null)
                return $document->type === 'QUOTATION' && is_null($document->signed_date);
            })
            ->each(function ($document) use ($projectEloquent, $parsedProjectNoData, $documentPrefix, $quotationPrefix, $agreementDate) {
                $documentAgreementNum = generateAgreementNumber('renovation_document', [
                    'company_initial' => $documentPrefix,
                    'quotation_initial' => $quotationPrefix,
                    'salesperson_initial' => $parsedProjectNoData['salesperson_initial'] ?? null,
                    'block_num' => $parsedProjectNoData['block_num'] ?? null,
                    'date' => $agreementDate,
                    'document_type' => 'QO',
                    'running_num' => $parsedProjectNoData['running_num'] ?? null,
                    'version_num' => $document->version_number,
                    'project_id' => $projectEloquent->id,
                    'quotation_num' => $parsedProjectNoData['quotation_num'] ?? null,
                    'project_agr_no' => $projectEloquent->agreement_no,
                    'common_project_running_number' => $parsedProjectNoData['common_project_running_num'] ?? null,
                    'common_quotation_running_number' => $parsedProjectNoData['common_quotation_running_num'] ?? null,
                ]);
                $document->agreement_no = $documentAgreementNum;
                $document->save();
            });
          
        
        }

        $projectEloquent->salespersons()->sync($salespersonIds);
        $exists_customer_ids = $projectEloquent->customersPivot()->pluck('user_id');
        $projectEloquent->customersPivot()->detach($exists_customer_ids);
        foreach ($customerIds as $customer) {
            $projectEloquent->customersPivot()->attach($customer['id'], ['property_id' => $customer['property_id']]);
        }

        return ProjectMapper::fromEloquent($projectEloquent);
    }

    public function destroy(int $project_id): void
    {

        $projectEloquent = ProjectEloquentModel::query()->findOrFail($project_id);

        (new DeletePropertyCommand($projectEloquent->property_id))->execute();

        $projectEloquent->salespersons()->detach();

        $projectEloquent->delete();
    }

    public function show(int $id): Object
    {
        $projectEloquent = ProjectEloquentModel::query()
            ->with(
                'salespersons.staffs.rank',
                'salespersons.roles',
                'properties',
                'company',
                'renovation_documents',
                'customers.customers',
                'contract',
                'projectRequirements',
                'designWorks',
                'hdbForms',
                'taxInvoices',
                'purchaseOrders',
                'contactUser',
                'projectPortFolios',
                'purchaseOrders'
            )
            ->findOrFail($id);


        $project = new ProjectDetailResource($projectEloquent);

        return $project;
    }

    public function getProjectById(int $id): Object
    {
        $project = ProjectEloquentModel::query()
            ->with(
                'supplierCostings',
                'saleReport'
            )
            ->findOrFail($id);

        return $project;
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

        $projectDetail = new ProjectDetailForHandoverResource($projectEloquent);

        return $projectDetail;
    }

    public function getCompanyStampByProjectId(int $projectId)
    {
        $projectEloquent = ProjectEloquentModel::find($projectId);

        $company = CompanyEloquentModel::find($projectEloquent->company_id);

        //$stampUrl = $company->company_stamp ? asset('storage/stamp/' . $company->company_stamp) : "-";

        $stampUrl = "-"; // Default value if the file doesn't exist
        if ($company->company_stamp && Storage::disk('public')->exists('stamp/' . $company->company_stamp)) {
            $stampUrl = base64_encode(Storage::disk('public')->get('stamp/' . $company->company_stamp));
        }

        return $stampUrl;
    }

    public function findOngoingProjects()
    {
        $projectEloquent = ProjectEloquentModel::where('project_status', '=', 'InProgress')->orderBy('created_at', 'desc')->take(3)->get();

        $ongoingProjects = ProjectResource::collection($projectEloquent);

        return $ongoingProjects;
    }

    public function cancelProject($projectId)
    {
        $project = ProjectEloquentModel::find($projectId);
        $project->update([
            'project_status' => 'Cancelled'
        ]);
        return $project;
    }

    public function ToggleFreezedProjectById($projectId)
    {
        $project = ProjectEloquentModel::find($projectId);
        $project->update([
            'freezed' => !$project->freezed
        ]);
        return $project;
    }

    public function retrieveProject($projectId)
    {
        $project = ProjectEloquentModel::find($projectId);
        $isProjectInprogress = $project->renovation_documents->contains(function ($document) {
            return $document->type === 'QUOTATION' && !is_null($document->signed_date);
        });
        $status = "New";
        if($isProjectInprogress){
            $status = "InProgress";
        }
        $project->update([
            'project_status' => $status
        ]);
        return $project;
    }

    public function getNewProjectList($perPage, $salePerson, $companyId, $filterText, $status, $filters)
    {
        $responseData = null;
        $statusMapping = getCurrentProjectStatusMap();
        if ($filters['cardView'] == 'true') {

            $authUser = auth('sanctum')->user();

            $authId = $authUser->id;

            if ($authUser->roles->contains('name', 'Management')) {
                if ($authUser->roles->contains('name', 'Salesperson')) {
                    $projects = ProjectEloquentModel::with('properties')
                    ->whereHas('salespersons', function ($query) use ($authId) {
                        $query->where('salesperson_id', $authId);
                    })
                        ->filter($filters)
                        ->get();
                } else {
                    $projects = ProjectEloquentModel::with('properties')->get();
                }
            } else {
                $projects = ProjectEloquentModel::with('properties')
                ->whereHas('salespersons', function ($query) use ($authId) {
                    $query->where('salesperson_id', $authId);
                })
                    ->filter($filters)
                    ->get();
            }

            $responseData = ProjectResource::collection($projects)
            ->filter(function ($project) use ($statusMapping) {
                return isset($statusMapping[$project->project_status]);
            })
            ->groupBy(function ($project) use ($statusMapping) {
                return $statusMapping[$project->project_status];
            });
        } else {
            $projectsQuery = ProjectEloquentModel::with('properties', 'customers', 'salespersons', 'renovation_documents.renovation_sections', 'saleReport.customer_payments.paymentType','saleReport')
                    ->filter($filters)->orderBy('project_status', 'DESC');

            if ($salePerson !== 0) {
                $projectsQuery->whereHas('salespersons', function ($query) use ($salePerson) {
                    $query->where('salesperson_id', $salePerson);
                });
            }

            if ($companyId !== 0) {
                $projectsQuery->where('company_id', $companyId);
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
                })->orWhere('invoice_no', 'LIKE', "%{$filterText}%");
            }

            if (!empty($status)) {
                if($status == 'approve_cancell_project'){
                    $projectsQuery->where('project_status', 'Cancelled')->orWhere('project_status', 'Pending');
                }else {
                    $projectsQuery->where('project_status', $status);
                }
            }

            $authUser = auth('sanctum')->user();

            if ($authUser->roles->contains('name', 'Manager') && $salePerson == 0) {
                $staff = $authUser->staffs;
                if ($staff) {
                    $projectsQuery->whereHas('salespersons', function ($query) use ($staff) {
                        $query->whereIn('users.id', function ($subQuery) use ($staff) {
                            $subQuery->select('user_id')
                            ->from('staffs')
                            ->where('mgr_id', $staff->user_id);
                        });
                    });
                }
            }
            $allowedStatuses = array_keys($statusMapping);
            if ($authUser->roles->contains('name', 'Accountant')) {
                $allowedStatuses = array_values(array_filter($allowedStatuses, fn($status) => $status !== 'New'));
            }
            $projects = $projectsQuery
                ->whereIn('project_status', $allowedStatuses)
                ->orderByRaw("CASE WHEN payment_status = 'REQUEST' THEN 1 WHEN payment_status = 'APPROVED' THEN 2 ELSE 3 END")
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            $finalResults = NewProjectResource::collection($projects);

            $links = [
                'first' => $projects->url(1),
                'last' => $projects->url($projects->lastPage()),
                'prev' => $projects->previousPageUrl(),
                'next' => $projects->nextPageUrl(),
            ];

            $meta = [
                'current_page' => $projects->currentPage(),
                'from' => $projects->firstItem(),
                'last_page' => $projects->lastPage(),
                'path' => $projects->url($projects->currentPage()),
                'per_page' => $perPage,
                'to' => $projects->lastItem(),
                'total' => $projects->total(),
            ];

            $responseData['data'] = $finalResults;
            $responseData['links'] = $links;
            $responseData['meta'] = $meta;
        }
        return $responseData;
    }

    public function getPendingCancelProjects($perPage)
    {
        $projects = ProjectEloquentModel::with('properties', 'customers', 'salespersons', 'renovation_documents.renovation_sections', 'saleReport.customer_payments.paymentType','saleReport')
                                            ->where('project_status', 'Cancelled')
                                            ->orWhere('project_status', 'Pending')
                                            ->paginate($perPage);

        $finalResults = NewProjectResource::collection($projects);

        $links = [
            'first' => $projects->url(1),
            'last' => $projects->url($projects->lastPage()),
            'prev' => $projects->previousPageUrl(),
            'next' => $projects->nextPageUrl(),
        ];

        $meta = [
            'current_page' => $projects->currentPage(),
            'from' => $projects->firstItem(),
            'last_page' => $projects->lastPage(),
            'path' => $projects->url($projects->currentPage()),
            'per_page' => $perPage,
            'to' => $projects->lastItem(),
            'total' => $projects->total(),
        ];

        $responseData['data'] = $finalResults;
        $responseData['links'] = $links;
        $responseData['meta'] = $meta;

        return $responseData;
    }

    public function pendingCancelProject($id)
    {
        $project = ProjectEloquentModel::find($id);
        if($project)
        {
            $project->update([
                'project_status' => 'Pending',
            ]);
        }
        return $project;
    }
}
