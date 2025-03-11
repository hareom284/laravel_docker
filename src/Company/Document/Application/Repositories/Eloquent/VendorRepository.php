<?php

namespace Src\Company\Document\Application\Repositories\Eloquent;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Src\Company\CompanyManagement\Domain\Repositories\AccountingServiceInterface;
use Src\Company\Document\Application\DTO\VendorData;
use Src\Company\Document\Application\Mappers\VendorMapper;
use Src\Company\Document\Domain\Model\Entities\Vendor;
use Src\Company\Document\Domain\Repositories\VendorRepositoryInterface;
use Src\Company\Document\Domain\Resources\VendorResource;
use Src\Company\Document\Infrastructure\EloquentModels\VendorEloquentModel;
use Src\Company\System\Infrastructure\EloquentModels\CompanyEloquentModel;
use Src\Company\System\Infrastructure\EloquentModels\GeneralSettingEloquentModel;

class VendorRepository implements VendorRepositoryInterface
{
    private $accountingService;

    public function __construct(AccountingServiceInterface $accountingService = null)
    {
        $this->accountingService = $accountingService;
    }

    public function getVendors($filters = [])
    {
        $vendorEloquent = VendorEloquentModel::query()
        ->filter($filters)
        ->orderBy('id', 'desc')->get();

        $finalResults = VendorResource::collection($vendorEloquent);

        return $finalResults;
    }

    public function getVendorById(int $vendor_id)
    {
        $vendorEloquent = VendorEloquentModel::query()->findOrFail($vendor_id);

        $finalResult = new VendorResource($vendorEloquent);

        return $finalResult;
    }

    public function getVendorByUserId(int $userId)
    {
        $vendorEloquent = VendorEloquentModel::query()->where('user_id', $userId)->first();

        $finalResult = new VendorResource($vendorEloquent);

        return $finalResult;
    }

    public function store(Vendor $vendor): VendorData
    {

        return DB::transaction(function () use ($vendor) {

            $vendorEloquent = VendorMapper::toEloquent($vendor);

            $generalSettingEloquent = GeneralSettingEloquentModel::where('setting', 'accounting_software_integration')->first();

            if($generalSettingEloquent && $generalSettingEloquent->value != 'none'){

                $quickbookVendor = [
                    'name' => $vendor->vendor_name,
                    'contact_person' => $vendor->contact_person,
                    'contact_person_last_name' => $vendor->contact_person_last_name,
                    'contact_no' => $vendor->contact_person_number,
                    'email' => $vendor->email,
                    'street_name' => $vendor->street_name,
                    'postal_code' => $vendor->postal_code,
                ];

                $totalCompanies = CompanyEloquentModel::all();

                if($totalCompanies->count() > 1){

                    foreach ($totalCompanies as $company) {
                        $this->accountingService->storeVendor($company->id, $quickbookVendor);
                    }

                }else{

                    $companyId = CompanyEloquentModel::where('is_default', 1)->first()->id;

                    $result = $this->accountingService->storeVendor($companyId, $quickbookVendor);

                    //Save the vendor id from accounting software to the database
                    if($generalSettingEloquent->value == 'quickbook'){
                        $vendorEloquent->quick_book_vendor_id = $result->Id;
                    }else{
                        $vendorEloquent->xero_vendor_id = $result;
                    }
                }
            }

            $vendorEloquent->save();

            return VendorData::fromEloquent($vendorEloquent);
        });
    }

    public function update(Vendor $vendor): VendorData
    {
        $oldVendorRecord = VendorEloquentModel::query()->findOrFail($vendor->id);

        $vendorEloquent = VendorMapper::toEloquent($vendor);

        $generalSettingEloquent = GeneralSettingEloquentModel::where('setting', 'accounting_software_integration')->first();

        if($generalSettingEloquent && $generalSettingEloquent->value != 'none'){

            if ($oldVendorRecord->vendor_name !== $vendor->vendor_name ||
            $oldVendorRecord->email !== $vendor->email ||
            $oldVendorRecord->street_name !== $vendor->street_name ||
            $oldVendorRecord->postal_code !== $vendor->postal_code) {

                $quickbookVendor = [
                    'name' => $vendor->vendor_name,
                    'contact_person' => $vendor->contact_person,
                    'contact_no' => $vendor->contact_person_number,
                    'email' => $vendor->email,
                    'street_name' => $vendor->street_name,
                    'postal_code' => $vendor->postal_code,
                ];
    
                $quickBookVendorId = $vendorEloquent->quick_book_vendor_id;
    
                $totalCompanies = CompanyEloquentModel::all();
    
                if($totalCompanies->count() > 1){
    
                    foreach ($totalCompanies as $company) {
    
                        $vendorFromQbo = $this->accountingService->getVendorByName($company->id,$vendor->vendor_name);
    
                        if($vendorFromQbo){
                            $this->accountingService->updateVendor($company->id,$quickbookVendor,$vendorFromQbo->Id);
                        }
                    }
    
                }else{
    
                    $companyId = CompanyEloquentModel::where('is_default', 1)->first()->id;
    
                    $this->accountingService->updateVendor($companyId,$quickbookVendor,$quickBookVendorId);
                }
            }
        }

        $vendorEloquent->save();

        return VendorData::fromEloquent($vendorEloquent);
    }

    public function delete(int $vendor_id): void
    {
        $vendorEloquent = VendorEloquentModel::query()->findOrFail($vendor_id);
        $vendorEloquent->delete();
    }

    public function syncVendorWithQuickBook()
    {
        $qboVendors = $this->accountingService->getAllVendors(2);

        foreach ($qboVendors as $vendor) {

            $isVendorExists = VendorEloquentModel::where('vendor_name', $vendor->DisplayName)->first();

            if(!$isVendorExists){

                VendorEloquentModel::create([
                    'vendor_name' => $vendor->DisplayName,
                    'contact_person' => $vendor->DisplayName,
                    'contact_person_number' => $vendor->PrimaryPhone->FreeFormNumber ?? 0,
                    'fax_number' => $vendor->PrimaryPhone->FreeFormNumber ?? 0,
                    'email' => $vendor->PrimaryEmailAddr->Address ?? null,
                    'rebate' => 0,
                    'vendor_category_id' => 1,
                    'quick_book_vendor_id' => $vendor->Id

                ]);
            }else{

                $isVendorExists->quick_book_vendor_id = $vendor->Id;

                $isVendorExists->update();
            }
        }

        return true;
    }

    public function syncWithAccountingSoftwareData($companyId)
    {
        $vendorsFromQbo = $this->accountingService->getAllVendors($companyId);

        Log::info("Vendor Counts: " . count($vendorsFromQbo));

        foreach ($vendorsFromQbo as $vendor) {

            $isVendorExists = VendorEloquentModel::where('vendor_name', $vendor->DisplayName)->first();

            if(!$isVendorExists){

                VendorEloquentModel::create([
                    'vendor_name' => $vendor->DisplayName,
                    'contact_person' => $vendor->DisplayName,
                    'contact_person_number' => 0,
                    'fax_number' => 0,
                    'email' => null,
                    'rebate' => 0,
                    'vendor_category_id' => 1,
                    'quick_book_vendor_id' => $vendor->Id

                ]);
            }else{

                $isVendorExists->quick_book_vendor_id = $vendor->Id;

                $isVendorExists->update();
            }
        }

        return $vendorsFromQbo;
    }
}
