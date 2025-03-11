<?php

namespace Src\Company\Document\Application\DTO;

use Illuminate\Http\Request;
use Src\Company\Document\Infrastructure\EloquentModels\QuotationTemplateCategoryEloquentModel;

class QuotationTemplateCategoryData
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly ?int $salesperson_id,
    ) {
    }

    public static function fromRequest(Request $request, ?int $quotation_template_category_id = null): QuotationTemplateCategoryData
    {
        return new self(
            id: $quotation_template_category_id,
            name: $request->string('name'),
            salesperson_id: $request->filled('salesperson_id') ? $request->integer('salesperson_id') : null

        );
    }

    public static function fromEloquent(QuotationTemplateCategoryEloquentModel $quotationTemplateCategoryEloquent): self
    {
        return new self(
            id: $quotationTemplateCategoryEloquent->id,
            name: $quotationTemplateCategoryEloquent->name,
            salesperson_id: $quotationTemplateCategoryEloquent->salesperson_id,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'salesperson_id' => $this->salesperson_id,
        ];
    }
}
