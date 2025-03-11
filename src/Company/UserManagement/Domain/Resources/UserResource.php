<?php

namespace Src\Company\UserManagement\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $profileUrl = $this->profile_pic ? asset('storage/profile_pic/' . $this->profile_pic) : null;
        $signatureBase64 = null;

        if($this->staffs && $this->staffs->signature) {
            $pathOfSignature = Storage::disk('public')->get('staff_signature/' . $this->staffs->signature) ;
            $signatureBase64 = 'data:image/png;base64,'.base64_encode($pathOfSignature);
        }

        $roleIds = $this->roles->pluck('id')->toArray();

        $countInProgress = 0;
        $countComplete = 0;

        if(isset($this->staffs))
        {
            foreach ($this->projects as $project) {
                if ($project['project_status'] === 'InProgress') {
                    $countInProgress++;
                } elseif ($project['project_status'] === 'Complete') {
                    $countComplete++;
                }
            }
        }

        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'prefix' => $this->prefix,
            'contact_no' => $this->contact_no,
            'profile_pic' => $profileUrl,
            'profile_image' => $this->profile_pic ?? null, // don't remove this, used in profile image update
            'name_prefix' => $this->name_prefix,
            'is_active' => $this->is_active,
            'userRoles' => $this->roles,
            'nric' => isset($this->customers) ? $this->customers->nric : "",
            'isCustomer' => isset($this->customers) ? true : false,
            'roles' => $roleIds,
            'staff_id' => isset($this->staffs) ? $this->staffs->id : "",
            'rank_name' => isset($this->staffs->rank) ? $this->staffs->rank->rank_name : "",
            'tier' => isset($this->staffs->rank) ? $this->staffs->rank->tier : "",
            'rank_id' => isset($this->staffs->rank) ? $this->staffs->rank->id : "",
            'commission' => $this->staffs->rank->commission_percent ?? "",
            'user_commission' => $this->commission,
            'signature' => $signatureBase64 ?? "",
            'mgr_id' => isset($this->staffs->mgr) ? $this->staffs->mgr_id : '',
            'assigned_user' => isset($this->staffs->mgr) ? $this->staffs->mgr : '',
            'assigned_saleperson' => isset($this->customers->staffs) ? $this->customers->staffs : [],
            'monthlySaleData' => isset($this->monthlyData) ? $this->monthlyData : 0,
            'yearlySaleData' => isset($this->yearlyData) ? $this->yearlyData : 0,
            'registryNo' => isset($this->staffs) ? $this->staffs->registry_no : "",
            'rank_updated_at' => isset($this->staffs->rank_updated_at) ? $this->staffs->rank_updated_at : '',
            'completed_project_count' => $countComplete,
            'inprogress_project_count' => $countInProgress,
        ];
    }
}
