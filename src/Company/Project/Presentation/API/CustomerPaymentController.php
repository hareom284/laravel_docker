<?php

namespace Src\Company\Project\Presentation\API;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Src\Company\Project\Application\Mappers\ProjectMapper;
use Src\Company\Project\Application\Mappers\PropertyMapper;
use Symfony\Component\HttpFoundation\Response;
use Src\Company\Project\Infrastructure\EloquentModels\CustomerPaymentEloquentModel;
use Illuminate\Support\Str;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\Document\Application\Mappers\DocumentMapper;
use Src\Company\Project\Domain\Repositories\CustomerPaymentRepositoryInterface;
use Src\Company\Document\Domain\Export\ExportInvoice;
use Maatwebsite\Excel\Facades\Excel;

class CustomerPaymentController extends Controller
{
    private $customerPaymentInterface;

    public function __construct(CustomerPaymentRepositoryInterface $customerPaymentInterface)
    {
        $this->customerPaymentInterface = $customerPaymentInterface;
    }

    public function exportInvoice(ExportInvoice $exportInvoice)
    {
        // Assuming you have a method to export or return the Excel file
        return Excel::download($exportInvoice, 'invoices.csv');
    }
}
