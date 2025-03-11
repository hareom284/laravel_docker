<?php

namespace Src\Company\Document\Application\Repositories\Eloquent;

use Src\Company\Document\Application\DTO\PaymentTermData;
use Src\Company\Document\Application\Mappers\PaymentTermMapper;
use Src\Company\Document\Domain\Model\Entities\PaymentTerm;
use Src\Company\Document\Domain\Repositories\PaymentTermRepositoryInterface;
use Src\Company\Document\Infrastructure\EloquentModels\PaymentTermEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\ProjectEloquentModel;

class PaymentTermRepository implements PaymentTermRepositoryInterface
{
    public function index($filters = [])
    {

        $paymentTermEloquent = PaymentTermEloquentModel::query()->filter($filters)->orderBy('id', 'desc');
        return $paymentTermEloquent->get();

    }

    public function findPaymentTermById(int $id)
    {
        $paymentTermEloquent = PaymentTermEloquentModel::query();

        return $paymentTermEloquent->findOrFail($id);
    }

    public function store(PaymentTerm $paymentTerm): PaymentTermData
    {

        $paymentTerm = PaymentTermMapper::toEloquent($paymentTerm);
        if($paymentTerm->is_default == 'true'){
            $is_already_default = PaymentTermEloquentModel::where('is_default', 1)->first();
            if($is_already_default){
                $is_already_default->update([
                    'is_default' => 0
                ]);
            }
        }
        $paymentTerm->is_default = $paymentTerm->is_default == 'true' ? 1 : 0;
        $paymentTerm->save();
        return PaymentTermData::fromEloquent($paymentTerm);
    }

    public function update(PaymentTerm $paymentTerm): PaymentTermData
    {
        $paymentTermEloquent = PaymentTermMapper::toEloquent($paymentTerm);
        if($paymentTerm->is_default == 'true'){
            $is_already_default = PaymentTermEloquentModel::where('is_default', 1);
            if($is_already_default){
                $is_already_default->update([
                    'is_default' => 0
                ]);
            }
        }
        $paymentTermEloquent->is_default = $paymentTerm->is_default == 'true' ? 1 : 0;
        $paymentTermEloquent->save();

        return PaymentTermData::fromEloquent($paymentTermEloquent);
    }

    public function delete(int $paymentTermId): void
    {
        $paymentTermEloquent = PaymentTermEloquentModel::query()->findOrFail($paymentTermId);
        $paymentTermEloquent->delete();
    }

    public function sendRequestNote($project_id, $request)
    {
        $project = ProjectEloquentModel::find($project_id);
        if($project){
            $project->update([
                'payment_status' => 'REQUEST',
                'request_note' => $request->request_note
            ]);
        }
    }

    public function approvePaymentRequest($project_id)
    {
        $project = ProjectEloquentModel::find($project_id);
        if($project){
            $project->update([
                'payment_status' => 'APPROVED'
            ]);
        }
    }
}
