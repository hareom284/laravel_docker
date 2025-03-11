<?php

namespace Src\Auth\Domain\Resources;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserMetaEloquentModel;

class AuthResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {


        /***
         *  see here more details on these documentation list
         *  https://github.com/chenming1337/design-den-vue/blob/feat/casl_implementatin/documentation/howtousecasl.md
         *  @hareom284
         */
        $permissionListCaslFormat = collect($this->permissions)->map(function ($action) {
            list($action, $subject) = explode('_', $action, 2);
            return ['action' => $action, 'subject' => $subject];
        });


        $profileUrl = $profileBase64 = null;
        if($this->profile_pic)
        {
            $profile_pic_file_path = 'profile_pic/' . $this->profile_pic;

            $profile_pic_image = Storage::disk('public')->get($profile_pic_file_path);

            $profileBase64 = base64_encode($profile_pic_image);
        }

        $signatureBase64 = null;

        if($this->staffs && $this->staffs->signature) {
            $pathOfSignature = Storage::disk('public')->get('staff_signature/' . $this->staffs->signature) ;
            $signatureBase64 = 'data:image/png;base64,'.base64_encode($pathOfSignature);
        }

        $profileUrl = $this->profile_pic ? asset('storage/profile_pic/' . $this->profile_pic) : null;

        $isSurveyAnswered = UserMetaEloquentModel::where('user_id', $this->id)->where('name', 'survey_answers')->first() ? true : false;


        return [
            'id' => $this->id,
            'name' => $this->first_name . ' ' .$this->last_name,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'profile_pic' => $profileBase64,
            'profile_url' => $profileUrl,
            'contact_no' => $this->contact_no,
            'signature' => $signatureBase64,
            'email' => $this->email,
            'roles' => $this->roles->pluck('name'),
            'permissions' => $permissionListCaslFormat->toArray(),
            'name_prefix' => $this->name_prefix,
            'survey_answered' => $isSurveyAnswered
        ];
    }

}
