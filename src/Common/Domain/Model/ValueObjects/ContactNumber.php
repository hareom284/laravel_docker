<?php

namespace Src\Common\Domain\Model\ValueObjects;

class ContactNumber
{
    private ?string $prefix;
    private string $contact_no;

    public function __construct(string $contact_no, ?string $prefix = null)
    {
        // You can add validation logic here
        $this->prefix = $prefix;
        $this->contact_no = $contact_no;
    }

    public function getPrefix(): ?string
    {
        return $this->prefix;
    }

    public function getContactNo(): string
    {
        return $this->contact_no;
    }

    public function __toString(): string
    {
        return $this->prefix ? "+{$this->prefix} {$this->contact_no}" : $this->contact_no;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['contact_no'],
            $data['prefix'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'prefix' => $this->prefix,
            'contact_no' => $this->contact_no,
        ];
    }
}
