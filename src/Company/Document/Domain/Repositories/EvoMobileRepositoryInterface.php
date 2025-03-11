<?php

namespace Src\Company\Document\Domain\Repositories;

interface EvoMobileRepositoryInterface
{
    public function getEvoAmt(int $projectId);
}
