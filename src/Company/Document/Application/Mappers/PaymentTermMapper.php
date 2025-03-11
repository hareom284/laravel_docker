<?php

namespace Src\Company\Document\Application\Mappers;
use Illuminate\Http\Request;
use Src\Company\Document\Domain\Model\Entities\PaymentTerm;
use Src\Company\Document\Infrastructure\EloquentModels\PaymentTermEloquentModel;

class PaymentTermMapper
{
    public static function fromRequest(Request $request, ?int $payment_term_id = null): PaymentTerm
    {
        return new PaymentTerm(
            id: $payment_term_id,
            name: $request->string('name'),
            payment_terms: $request->string('payment_terms'),
            project_id: $request->project_id,
            is_default: $request->is_default
        );
    }

    public static function fromEloquent(PaymentTermEloquentModel $teamEloquent): PaymentTerm
    {
        return new PaymentTerm(
            id: $teamEloquent->id,
            name:$teamEloquent->name,
            payment_terms: $teamEloquent->payment_terms,
            project_id: $teamEloquent->project_id,
            is_default: $teamEloquent->is_default
        );
    }

    public static function toEloquent(PaymentTerm $paymentTerm): PaymentTermEloquentModel
    {
        $paymentTermEloquent = new PaymentTermEloquentModel();
        if ($paymentTerm->id) {
            $paymentTermEloquent = PaymentTermEloquentModel::query()->findOrFail($paymentTerm->id);
        }
        $paymentTermEloquent->name = $paymentTerm->name;
        $paymentTermEloquent->payment_terms = $paymentTerm->payment_terms;
        $paymentTermEloquent->project_id = $paymentTerm->project_id;
        $paymentTermEloquent->is_default = $paymentTerm->is_default;
        return $paymentTermEloquent;
    }
}
