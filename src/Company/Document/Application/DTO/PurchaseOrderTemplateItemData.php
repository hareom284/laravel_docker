<?php

namespace Src\Company\Document\Application\DTO;

use Illuminate\Http\Request;
use Src\Company\Document\Infrastructure\EloquentModels\PurchaseOrderTemplateItemEloquentModel;

class PurchaseOrderTemplateItemData
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $vendor_category_id,
        public readonly int $company_id,
        public readonly string $description,
        public readonly ?string $quantity,
        public readonly ?string $code,
        public readonly ?string $size
    ) {
    }

    public static function fromRequest(Request $request, ?int $poTemplateItemId = null): PurchaseOrderTemplateItemData
    {
        return new self(
            id: $poTemplateItemId,
            vendor_category_id: $request->integer('vendor_category_id'),
            company_id: $request->integer('company_id'),
            description: $request->string('description'),
            quantity: $request->string('quantity'),
            code: $request->string('code'),
            size: $request->string('size')
        );
    }

    public static function fromEloquent(PurchaseOrderTemplateItemEloquentModel $poTemplateItemEloquent): self
    {
        return new self(
            id: $poTemplateItemEloquent->id,
            vendor_category_id: $poTemplateItemEloquent->vendor_category_id,
            company_id: $poTemplateItemEloquent->company_id,
            description: $poTemplateItemEloquent->description,
            quantity: $poTemplateItemEloquent->quantity,
            code: $poTemplateItemEloquent->code,
            size: $poTemplateItemEloquent->size,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'vendor_category_id' => $this->vendor_category_id,
            'company_id' => $this->company_id,
            'description' => $this->description,
            'quantity' => $this->quantity,
            'code' => $this->code,
            'size' => $this->size
        ];
    }
}
