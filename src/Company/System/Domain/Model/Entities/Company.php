<?php

namespace Src\Company\System\Domain\Model\Entities;

use Src\Common\Domain\Entity;

class Company extends Entity
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly ?string $tel,
        public readonly ?string $fax,
        public readonly ?string $email,
        public readonly ?string $main_office,
        public readonly ?string $design_branch_studio,
        public readonly ?string $hdb_license_no,
        public readonly ?string $reg_no,
        public readonly ?string $gst_reg_no,
        public readonly ?float $gst,
        public readonly ?string $logo,
        public readonly ?string $company_stamp,
        public readonly ?string $docu_prefix,
        public readonly ?int $invoice_no_start,
        public readonly ?string $fy_start,
        public readonly ?string $fy_end,
        public readonly ?int $quotation_no,
        public readonly ?string $customer_invoice_running_number_values,
        public readonly ?bool $enable_customer_running_number_by_month,
        public readonly ?string $quotation_prefix,
        public readonly ?string $invoice_prefix,
        public readonly ?int $invoice_running_number,
    ) {}



    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'tel' => $this->tel,
            'fax' => $this->fax,
            'email' => $this->email,
            'main_office' => $this->main_office,
            'design_branch_studio' => $this->design_branch_studio,
            'hdb_license_no' => $this->hdb_license_no,
            'reg_no' => $this->reg_no,
            'gst_reg_no' => $this->gst_reg_no,
            'gst' => $this->gst,
            'logo' => $this->logo,
            'company_stamp' => $this->company_stamp,
            'docu_prefix' => $this->docu_prefix,
            'invoice_no_start' => $this->invoice_no_start,
            'fy_start' => $this->fy_start,
            'fy_end' => $this->fy_end,
            'quotation_no' => $this->quotation_no,
            'customer_invoice_running_number_values' => $this->customer_invoice_running_number_values,
            'enable_customer_running_number_by_month' => $this->enable_customer_running_number_by_month,
            'quotation_prefix' => $this->quotation_prefix,
            'invoice_prefix' => $this->invoice_prefix,
            'invoice_running_number' => $this->invoice_running_number
        ];
    }
}
