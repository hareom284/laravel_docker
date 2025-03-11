<?php

namespace Src\Company\CustomerManagement\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\CustomerManagement\Domain\Repositories\CustomerRepositoryInterface;

class SendSuccessCreateLeadMailToCustomerCommand implements CommandInterface
{
    private CustomerRepositoryInterface $repository;

    public function __construct(
        private readonly string $name,
        private readonly string $email,
        private readonly string $password,
        private readonly string $siteSetting,
        private readonly array $salespersonNames,
    )
    {
        $this->repository = app()->make(CustomerRepositoryInterface::class);
    }

    public function execute()
    {

        return $this->repository->sendSuccessMail($this->name, $this->email,$this->password, $this->siteSetting, $this->salespersonNames);
    }
}
