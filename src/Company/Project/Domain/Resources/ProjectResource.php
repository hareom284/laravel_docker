<?php

namespace Src\Company\Project\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Src\Company\Document\Infrastructure\EloquentModels\RenovationDocumentsEloquentModel;
use Src\Company\Project\Application\UseCases\Queries\FindCustomerPaymentMobileQuery;
use Src\Company\Project\Application\UseCases\Queries\FindSupplierCostingByProjectIdMobileQuery;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */

    public function toArray($request)
    {
        // $dueDate = date("d M Y", strtotime($this->expected_date_of_completion));

        // $dueDate = date("d/m/Y", strtotime($this->expected_date_of_completion));
        $customers_array = [];
        if ($this->customersPivot) {
            foreach ($this->customersPivot as $customer) {
                $customer_detail = [
                    'customerName' => $customer->name_prefix . ' ' . $customer->first_name . ' ' . $customer->last_name,
                    'customerEmail' => $customer->email,
                    'customerPhone' => $customer->contact_no,
                    'customerPhonePrefix' => $customer->prefix
                ];
                array_push($customers_array, $customer_detail);
            }
        }
        $customerName = $this->customers->name_prefix . ' ' . $this->customers->first_name . ' ' . $this->customers->last_name;

        $saleperson_array = [];
        if(isset($this->salespersons)){
            foreach ($this->salespersons as $saleperson) {
                $saleperson_detail = [
                    'salepersonName' => $saleperson->name_prefix . ' ' . $saleperson->first_name . ' ' . $saleperson->last_name,
                    'salepersonEmail' => $saleperson->email,
                    'prefix' => $saleperson->prefix,
                    'salepersonPhone' => $saleperson->contact_no
                ];
                array_push($saleperson_array, $saleperson_detail);
            }
        }

        $is_signed_quotation = RenovationDocumentsEloquentModel::where('project_id', $this->id)
            ->where('type', 'QUOTATION')
            ->whereNotNull('signed_date')
            ->first(['signed_date']);

        switch ($this->project_status) {
            case 'New':
                $created_date = date("d/m/Y", strtotime($this->created_at));
                break;

            case 'InProgress':

                $created_date = $is_signed_quotation ? date("d/m/Y", strtotime($is_signed_quotation->signed_date)) : '';

            case 'Completed':
                $created_date = date("d/m/Y", strtotime($this->updated_at));
                break;

            case 'Cancelled':
                $created_date = date("d/m/Y", strtotime($this->updated_at));
                break;

            default:
                $created_date = date("d/m/Y", strtotime($this->updated_at));
                break;
        }

        return [
            'id' => $this->id,
            'street_name' => $this->properties->street_name,
            'block_num' => $this->properties->block_num,
            'unit_num' => $this->properties->unit_num,
            'postal_code' => $this->properties->postal_code,
            'customerName' => $customerName,
            'customerEmail' => $this->customers->email,
            'customerPhonePrefix' => $this->customers->prefix,
            'customerPhone' => $this->customers->contact_no,
            'property_type' => $this->properties->propertyType->type,
            'project_status' => $this->project_status,
            'customers_array' => $customers_array,
            'description' => $this->description,
            'freezed' => $this->freezed,
            'saleperson_array' => $saleperson_array,
            'customer_payment' => (new FindCustomerPaymentMobileQuery($this->id))->handle(),
            'supplier_cost'   =>  (new FindSupplierCostingByProjectIdMobileQuery($this->id))->handle(),
            // 'due_date' => $dueDate,
            // 'signed_date' => isset($is_signed_quotation) ? date("d/m/Y", strtotime($is_signed_quotation->signed_date)) : '',
            // 'created_date' => date("d/m/Y", strtotime($this->created_at)),
            // 'created_or_signed_date' => $this->project_status == 'New' ? date("d/m/Y", strtotime($this->created_at)) : date("d/m/Y", strtotime($is_signed_quotation->signed_date))
            'created_at' => $created_date,
        ];
    }
}
