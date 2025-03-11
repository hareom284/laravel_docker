<?php

namespace Src\Company\Document\Domain\Repositories;

use Src\Company\Document\Application\DTO\PaymentTermData;
use Src\Company\Document\Domain\Model\Entities\PaymentTerm;

interface PaymentTermRepositoryInterface
{
    public function index($filters=[]);

    public function findPaymentTermById(int $id);

    public function store(PaymentTerm $paymentTerm): PaymentTermData;

    public function update(PaymentTerm $paymentTerm): PaymentTermData;

    public function delete(int $paymentTermId);
    
    public function sendRequestNote($project_id, $request);

    public function approvePaymentRequest($project_id);

}
