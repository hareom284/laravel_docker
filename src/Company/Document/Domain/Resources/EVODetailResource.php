<?php

namespace Src\Company\Document\Domain\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Src\Company\Document\Infrastructure\EloquentModels\DocumentStandardEloquentModel;

class EVODetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // $customer_base64Image = '';
        // if($this->customer_signature)
        // {
        //     $customer_file_path = 'EVO/customer_signature_file/' . $this->customer_signature;

        //     $customer_image = Storage::disk('public')->get($customer_file_path);

        //     $customer_base64Image = base64_encode($customer_image);
        // }
        $customer_base64Image = [];
        if (isset($this->customer_signatures) && count($this->customer_signatures) > 0) {
            foreach ($this->customer_signatures as $customer_sign) {
                $customer_file_path = 'EVO/customer_signature_file/' . $customer_sign->customer_signature;

                $customer_image = Storage::disk('public')->get($customer_file_path);


                array_push($customer_base64Image, [
                    'customer' => $customer_sign->customer,
                    'customer_signature' => base64_encode($customer_image)
                ]);
            }
        } else if ($this->customer_signature) {
            $customer_file_path = 'EVO/customer_signature_file/' . $this->customer_signature;

            $customer_image = Storage::disk('public')->get($customer_file_path);


            array_push($customer_base64Image, [
                'customer' => $this->projects->customer,
                'customer_signature' => base64_encode($customer_image)
            ]);
        }
        $sale_file_path = 'EVO/salesperson_signature_file/' . $this->salesperson_signature;

        $sale_image = Storage::disk('public')->get($sale_file_path);

        $sale_base64Image = base64_encode($sale_image);

        $documentStandard = DocumentStandardEloquentModel::where('company_id', $this->projects->company_id)->where('name', "electrical variation order")->first(['header_text', 'footer_text', 'disclaimer']);
        $pdf_file = "";
        if ($this->pdf_file) {
            $pdf_file =  asset('storage/pdfs/' . $this->pdf_file);
        }
        return [

            'id' => $this->id,
            'version_num' => $this->version_number,
            'total_amount' => $this->total_amount,
            'grand_total' => $this->grand_total,
            'saleperson_image' => $sale_base64Image,
            'customer_image' => $customer_base64Image,
            'signed_date' => $this->signed_date ? $this->signed_date : $this->created_at,
            'created_date' => $this->created_at,
            'already_sign' => $this->signed_date ? true : false,
            'evo_items' => EVOItemsDetailResource::collection($this->evo_items),
            'signed_saleperson' => $this->salesperson->user->first_name . ' ' . $this->salesperson->user->last_name,
            'signed_sale_email' => $this->salesperson->user->email,
            'signed_sale_ph' => $this->salesperson->user->contact_no,
            'rank' => $this->salesperson->rank->rank_name,
            'header_text' => $documentStandard ? $documentStandard->header_text : '',
            'footer_text' => $documentStandard ? $documentStandard->footer_text : '',
            'disclaimer' => $documentStandard ? $documentStandard->disclaimer : '',
            'pdf_file' => $pdf_file
        ];
    }
}
