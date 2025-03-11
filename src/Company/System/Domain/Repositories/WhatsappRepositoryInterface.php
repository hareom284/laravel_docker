<?php

namespace Src\Company\System\Domain\Repositories;

interface WhatsappRepositoryInterface
{
    public function sendTemplateMessage($template_name,$language_code,$to,$name,$file_url,$document_type,$total_amount);
}
