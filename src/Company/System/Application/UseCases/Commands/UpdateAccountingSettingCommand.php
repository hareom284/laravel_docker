<?php

namespace Src\Company\System\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\System\Domain\Repositories\AccountingSettingRepositoryInterface;

class UpdateAccountingSettingCommand implements CommandInterface
{
    private AccountingSettingRepositoryInterface $repository;

    public function __construct(
        private readonly array $settings
    )
    {
        $this->repository = app()->make(AccountingSettingRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->update($this->settings);
    }
}
