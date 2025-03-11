<?php

namespace Src\Company\UserManagement\Domain\Model;

use Src\Common\Domain\AggregateRoot;
use Src\Common\Domain\Model\ValueObjects\ContactNumber;

class User extends AggregateRoot implements \JsonSerializable
{
    public function __construct(
        public readonly ?int $id,
        public readonly ?string $first_name,
        public readonly ?string $last_name,
        public readonly ?string $email,
        public readonly ContactNumber $contact_no,
        public readonly ?string $profile_pic,
        public readonly ?string $name_prefix,
        public readonly string $is_active,
        public readonly ?int $quick_book_user_id,
        public readonly ?int $commission
    )
    {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'prefix' => $this->contact_no->getPrefix(),
            'contact_no' => $this->contact_no->getContactNo(),
            'name_prefix' => $this->name_prefix,
            'is_active' => $this->is_active,
            'quick_book_user_id' => $this->quick_book_user_id,
            'commission' => $this->commission
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
