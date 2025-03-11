<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Repositories\RenovationDocumentInterface;

class SendEmailCopyCommand implements CommandInterface
{
    private RenovationDocumentInterface $repository;

    public function __construct(
        private readonly int $projectId,
        private readonly string $email,
        private readonly string $attachment
    )
    {
        $this->repository = app()->make(RenovationDocumentInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->sendEmailCopy($this->projectId,$this->email,$this->attachment);
    }
}