<?php

namespace Src\Company\System\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\System\Domain\Repositories\WhatsappRepositoryInterface;

class SendTemplateMessageCommand implements CommandInterface
{
    private WhatsappRepositoryInterface $repository;

    public function __construct(
        private readonly string $template_name,
        private readonly string $language_code,
        private readonly string $to,
        private readonly string $name,
        private readonly string $file_url,
        private readonly string $document_type,
        private readonly int $total_amount,
    )
    {
        $this->repository = app()->make(WhatsappRepositoryInterface::class);
    }

    public function execute()
    {
        return $this->repository->sendTemplateMessage($this->template_name,$this->language_code,$this->to,$this->name,$this->file_url,$this->document_type,$this->total_amount);
    }
}
