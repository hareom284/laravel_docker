<?php

namespace Src\Company\Document\Domain\Repositories;

use Src\Company\Document\Application\DTO\HandoverCertificateData;
use Src\Company\Document\Application\DTO\PurchaseOrderData;
use Src\Company\Document\Domain\Model\Entities\HandoverCertificate;
use Src\Company\Document\Domain\Model\Entities\PurchaseOrder;

interface HandoverCertificateRepositoryInterface
{

	public function index();

	public function getHandoverByProjectId(int $projectId);

	public function getApproveHandoverCertificates();

	public function managerSign($request);

	public function customerSign($request);

	public function getHandoverCertificateDetail(int $id);

	public function salepersonSign($request);

	public function handoverSign($request);

}