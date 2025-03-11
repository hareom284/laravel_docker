<?php

namespace Src\Company\Security\Application\DTO;

use Illuminate\Http\Request;
use Src\Company\Security\Infrastructure\EloquentModels\UserEloquentModel;

class UserData
{

    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly ?int $organization_id,
        public readonly ?string $email_verified_at,
        public readonly ?string $dob,
        public readonly ?string $contact_number,
        public readonly ?int $storage_limit,
        public readonly ?string $password,
        public readonly ?string $is_active,
        public readonly ?int $stripe_id,
        public readonly ?string $pm_brand,
        public readonly ?string $pm_last_four,
        public readonly ?string $trial_end_at,
    ) {
    }

    public static function fromRequest(Request $request, $user_id = null): UserData
    {

        return new self(
            id: $user_id,
            name: $request->name,
            email: $request->email,
            organization_id: $request->organization_id,
            email_verified_at: $request->email_verified_at,
            dob: $request->dob,
            contact_number: $request->contact_number,
            storage_limit: $request->storage_limit,
            password: $request->password,
            is_active: $request->is_active,
            stripe_id: $request->stripe_id,
            pm_brand: $request->pm_brand,
            pm_last_four: $request->pm_last_four,
            trial_end_at: $request->trial_end_at,
        );
    }

    public static function fromEloquent(UserEloquentModel $user): self
    {
        return new self(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            organization_id: $user->organization_id,
            email_verified_at: $user->email_verified_at,
            dob: $user->dob,
            contact_number: $user->contact_number,
            storage_limit: $user->storage_limit,
            password: $user->password,
            is_active: $user->is_active,
            stripe_id: $user->stripe_id,
            pm_brand: $user->pm_brand,
            pm_last_four: $user->pm_last_four,
            trial_end_at: $user->trial_end_at,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            "email" => $this->email,
            "organization_id"  => $this->organization_id,
            "email_verified_at" => $this->email_verified_at,
            "dob" => $this->dob,
            "contact_number"  => $this->contact_number,
            "storage_limit" => $this->storage_limit,
            "password"  => $this->password,
            "is_active" => $this->is_active,
            "stripe_id" => $this->stripe_id,
            "pm_brand" => $this->pm_brand,
            "pm_last_four" => $this->pm_last_four,
            "trial_end_at" => $this->trial_end_at,
        ];
    }
}
