<?php

namespace Src\Company\System\Application\Repositories\Eloquent;

use Illuminate\Support\Facades\DB;

use Src\Company\System\Domain\Repositories\CompanyMobileRepositoryInterface;
use Src\Company\System\Domain\Resources\CompanyMobileResource;
use Src\Company\System\Infrastructure\EloquentModels\CompanyEloquentModel;
use Src\Company\System\Infrastructure\EloquentModels\GeneralSettingEloquentModel;

class CompanyMobileRepository implements CompanyMobileRepositoryInterface
{

    public function getCompanies($filters = [])
    {
        //companies list
        $perPage = $filters['perPage'] ?? 10;

        $companyEloquent = CompanyEloquentModel::filter($filters)->orderBy('id', 'desc')->paginate($perPage);

        $companies = CompanyMobileResource::collection($companyEloquent);

        $links = [
            'first' => $companies->url(1),
            'last' => $companies->url($companies->lastPage()),
            'prev' => $companies->previousPageUrl(),
            'next' => $companies->nextPageUrl(),
        ];
        $meta = [
            'current_page' => $companies->currentPage(),
            'from' => $companies->firstItem(),
            'last_page' => $companies->lastPage(),
            'path' => $companies->url($companies->currentPage()),
            'per_page' => $perPage,
            'to' => $companies->lastItem(),
            'total' => $companies->total(),
        ];
        $responseData['data'] = $companies;
        $responseData['links'] = $links;
        $responseData['meta'] = $meta;
        
        return $responseData;
    }

    public function increaseQuotationNo(int $company_id)
    {
        $checkCommonQuotationNumSetting = GeneralSettingEloquentModel::where('setting', "enable_common_quotation_running_number")
                                            ->where('value', "true")
                                            ->first();
        if($checkCommonQuotationNumSetting){
            $commonQuotationNum = GeneralSettingEloquentModel::where('setting','common_quotation_start_number')->first();
            $commonQuotationNum->increment('value');
        }else{
            $companyEloquent = CompanyEloquentModel::query()->findOrFail($company_id);
            $companyEloquent->increment('quotation_no');
        }
    }
}