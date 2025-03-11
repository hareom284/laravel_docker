<?php

namespace Src\Company\Document\Domain\Repositories;

interface HandoverCertificateMobileRepositoryInterface
{

	public function getHandoverByProjectId(int $projectId);

	public function getHandoverCertificateDetail(int $id);

	public function salepersonSign($request);

	public function customerSign($request);

}