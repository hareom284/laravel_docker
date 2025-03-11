<?php

namespace Src\Company\UserManagement\Application\DTO;

use Illuminate\Http\Request;
use Src\Common\Domain\Model\ValueObjects\ContactNumber;
use Src\Company\System\Infrastructure\EloquentModels\CompanyEloquentModel;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;

class UserData
{
    public function __construct(
        public readonly ?int $id,
        public readonly ?string $first_name,
        public readonly ?string $last_name,
        public readonly ?string $email,
        public readonly ContactNumber $contact_no,
        public readonly ?string $is_active,
        public readonly ?string $name_prefix,
        public readonly ?string $profile_pic,
        public readonly ?int $quick_book_user_id,
        public readonly ?int $commission
    )
    {}

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

    public static function fromEloquent(UserEloquentModel $userEloquentModel): self
    {

        $contact_no = new ContactNumber(
            prefix: $userEloquentModel->prefix,
            contact_no: $userEloquentModel->contact_no
        );
        return new self(
            id: $userEloquentModel->id,
            first_name: $userEloquentModel->first_name,
            last_name: $userEloquentModel->last_name,
            email: $userEloquentModel->email,
            contact_no: $contact_no,
            is_active: $userEloquentModel->is_active,
            name_prefix: $userEloquentModel->name_prefix,
            profile_pic: $userEloquentModel->profile_pic,
            quick_book_user_id: $userEloquentModel->quick_book_user_id,
            commission: $userEloquentModel->commission

        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'prefix' => $this->contact_no->getPrefix(),
            'contact_no' => $this->contact_no->getContactNo(),
            'is_active' => $this->is_active,
            'name_prefix' => $this->name_prefix,
            'profile_pic' => $this->profile_pic,
            'quick_book_user_id' => $this->quick_book_user_id,
            'commission' => $this->commission
        ];
    }
}
