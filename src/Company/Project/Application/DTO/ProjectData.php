<?php

namespace Src\Company\Project\Application\DTO;

use Illuminate\Http\Request;
use Src\Company\User\Infrastructure\EloquentModels\CompanyEloquentModel;

class ProjectData
{
    public function __construct(
        public readonly ?int $id,
        public readonly ?string $invoice_no,
        public readonly string $description,
        public readonly string $collection_of_keys,
        public readonly string $expected_date_of_completion,
        public readonly ?string $completed_date = null,
        public readonly string $project_status,
        public readonly string|int|null $customer_id,
        public readonly int $company_id,
        public readonly ?string $payment_status,
        public readonly ?string $request_note,
        public readonly ?int $term_and_condition_id
    ) {
    }

    // public static function fromRequest(Request $request, ?int $company_id = null): CompanyData
    // {
    //     return new self(
    //         id: $company_id,
    //         name: $request->string('name'),
    //         tel: $request->string('tel'),
    //         fax: $request->string('fax'),
    //         email: $request->string('email'),
    //         main_office: $request->string('main_office'),
    //         design_branch_studio: $request->string('design_branch_studio'),
    //         hdb_license_no: $request->string('hdb_license_no'),
    //         reg_no: $request->string('reg_no'),
    //         gst_reg_no: $request->string('gst_reg_no'),
    //         logo: $request->string('logo')
    //     );
    // }

    // public static function fromEloquent(CompanyEloquentModel $companyEloquent): self
    // {
    //     return new self(
    //         id: $companyEloquent->id,
    //         name: $companyEloquent->name,
    //         tel: $companyEloquent->tel,
    //         fax: $companyEloquent->fax,
    //         email: $companyEloquent->email,
    //         main_office: $companyEloquent->main_office,
    //         design_branch_studio: $companyEloquent->design_branch_studio,
    //         hdb_license_no: $companyEloquent->hdb_license_no,
    //         reg_no: $companyEloquent->reg_no,
    //         gst_reg_no: $companyEloquent->gst_reg_no,
    //         logo: $companyEloquent->logo
    //     );
    // }

    // public function toArray(): array
    // {
    //     return [
    //         'id' => $this->id,
    //         'name' => $this->name,
    //         'tel' => $this->tel,
    //         'fax' => $this->fax,
    //         'email' => $this->email,
    //         'main_office' => $this->main_office,
    //         'design_branch_studio' => $this->design_branch_studio,
    //         'hdb_license_no' => $this->hdb_license_no,
    //         'reg_no' => $this->reg_no,
    //         'gst_reg_no' => $this->gst_reg_no,
    //         'logo' => $this->logo
    //     ];
    // }
}
