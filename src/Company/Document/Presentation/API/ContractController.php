<?php

namespace Src\Company\Document\Presentation\API;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Src\Common\Infrastructure\Laravel\Controller;
use Symfony\Component\HttpFoundation\Response;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Src\Company\Document\Application\Mappers\ContractMapper;
use Src\Company\Document\Application\Mappers\QuotationTemplateItemsMapper;
use Src\Company\Document\Application\UseCases\Commands\SignContractCommand;
use Src\Company\Document\Application\UseCases\Commands\StoreContractCommand;
use Src\Company\Document\Application\UseCases\Commands\StoreQuotationTemplateItemsCommand;
use Src\Company\Document\Application\UseCases\Queries\FindAllContractQuery;
use Src\Company\Document\Application\UseCases\Queries\FindContractByIdQuery;
use Src\Company\Document\Domain\Resources\ContractResource;
use Src\Company\Document\Infrastructure\EloquentModels\ContractEloquentModel;
use Src\Company\Document\Application\Policies\ProjectContractPolicy;
use Src\Company\Document\Application\UseCases\Commands\SignCustomerContractCommand;
use Illuminate\Support\Facades\Log;
use Src\Company\Document\Application\UseCases\Queries\FindSignedQuotationDocumentQuery;
use Src\Company\Project\Application\UseCases\Queries\FindProjectByIdQuery;

class ContractController extends Controller
{

    public function index($projectId): JsonResponse
    {
        //check if user's has permission
        abort_if(authorize('view', ProjectContractPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Contract!');

        try {

            $contracts = (new FindAllContractQuery($projectId))->handle();

            return response()->success($contracts, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function store(Request $request): JsonResponse
    {
        //check if user's has permission
        abort_if(authorize('generate', ProjectContractPolicy::class), Response::HTTP_FORBIDDEN, 'Need generate permission for Contract!');

        try {
            $contract = ContractMapper::fromRequest($request);

            $contractData = (new StoreContractCommand($contract))->execute();
            $this->downloadPdf($request);
            $this->downloadContractPdf($request);

            return response()->success($contractData, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function signContract(Request $request)
    {

        //check if user's has permission
        abort_if(authorize('sign', ProjectContractPolicy::class), Response::HTTP_FORBIDDEN, 'Need sign permission for Contract!');

        try {

            $contractData = (new SignContractCommand($request))->execute();

            $contract = new ContractResource($contractData);
            $this->downloadPdf($request);
            $this->downloadContractPdf($request);
            return response()->success($contract, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function customerSign(Request $request)
    {
        abort_if(authorize('sign', ProjectContractPolicy::class), Response::HTTP_FORBIDDEN, 'Need sign permission for Contract!'); //need to recheck whether new per need or not

        try {

            $contractData = (new SignCustomerContractCommand($request))->execute();

            $contract = new ContractResource($contractData);

            $this->downloadPdf($request);
            $this->downloadContractPdf($request);

            return response()->success($contract, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function contractDetail($contractId): JsonResponse
    {
        //check if user's has permission
        abort_if(authorize('view', ProjectContractPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Contract!');

        try {

            $contract = (new FindContractByIdQuery($contractId))->handle();

            return response()->success($contract, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }
    public function getCompanyLogo($company_logo)
    {
        if ($company_logo) {
            $customer_file_path = 'logo/' . $company_logo;

            $company_image = Storage::disk('public')->get($customer_file_path);

            $company_base64Image = base64_encode($company_image);
            return $company_base64Image;
        }
    }
    public function downloadPdf(Request $request)
    {
        $projectId = $request->project_id;
        $contracts = collect((new FindAllContractQuery($projectId))->handle());
        $project = (new FindProjectByIdQuery($projectId))->handle();

        $folder_name  = env('COMPANY_FOLDER_NAME', 'Twp');

        $documentData = [
            "customer_name" => $project->contract->name,
            "nric" => $project->contract->nric,
            "company" => $project->contract->company,
            "address" => $project->contract->address,
            "created_date" => $project->contract->created_at,
            "pdpa_authorization" => $project->contract->pdpa_authorization
        ];
        $contractList = [
            "encode_owner_signature" => $contracts['encode_owner_signature'],
            "customer_name" => $contracts['customer_name'],
            "encode_contractor_signature" => $contracts['encode_contractor_signature'],
            "company_name" => $contracts['company_name'],
        ];
        $data = [
            "documentData" => $documentData,
            "contractLists" => $contractList,
            "folder_name" => $folder_name,
            "companies" => $project->company,
            "company_logo" => $this->getCompanyLogo($project->company->logo),
        ];
        $pdf = \PDF::loadView('pdf.PDPA.pdpa', $data);

        $pdfDocument = ContractEloquentModel::find($contracts['id']);
        $fileName = 'pdpa' . time() . '.pdf';
        $filePath = 'pdfs/' . $fileName;
        Storage::disk('public')->put($filePath, $pdf->output());
        // Store the file path in the database (assuming you have a model PdfDocument)
        if ($pdfDocument) {
            // Check if the old PDF file exists and delete it
            if (!empty($pdfDocument->pdf_file) && Storage::disk('public')->exists('pdfs/' . $pdfDocument->pdf_file)) {
                Storage::disk('public')->delete('pdfs/' . $pdfDocument->pdf_file);
            }
            // Update the database with the new file name
            $pdfDocument->update([
                'pdpa_pdf_file' => $fileName
            ]);
        }
        return $pdf->download("PDPA.pdf");
    }

    public function pdfTest(){
        $pdf = \PDF::loadView('pdf.TERM&CONDITION.term&condition');

        $fileName = 'Test.pdf';
        $filePath = 'pdfs/' . $fileName;
        Storage::disk('public')->put($filePath, $pdf->output());

        return $pdf->download("PDPA.pdf");
    }


    public function getDate($date)
    {
        $dateObject = new \DateTime($date);

        // In PHP, format 'j' will return the day of the month without leading zeros
        $day = $dateObject->format('j');

        return $this->getOrdinalDay($day);
    }

    protected function getOrdinalDay($day)
    {
        if ($day >= 11 && $day <= 13) {
            return $day . 'th';
        }

        $lastDigit = $day % 10;

        switch ($lastDigit) {
            case 1:
                return $day . 'st';
            case 2:
                return $day . 'nd';
            case 3:
                return $day . 'rd';
            default:
                return $day . 'th';
        }
    }
    public function getMonth($date)
    {
        $dateObject = new \DateTime($date);

        // PHP's 'M' format character gives you the abbreviated month name
        return $dateObject->format('M');
    }

    public function numberToWords($number)
    {
        $units = ['', 'Thousand', 'Million', 'Billion'];
        $words = [];

        if ($number == 0) {
            return 'Zero Dollars';
        }

        $dollars = floor($number);
        $cents = round(($number - $dollars) * 100);

        $unitIndex = 0;
        while ($dollars > 0) {
            $chunk = $dollars % 1000;
            if ($chunk !== 0) {
                array_unshift($words, $this->chunkToWords($chunk) . ' ' . $units[$unitIndex]);
            }
            $dollars = floor($dollars / 1000);
            $unitIndex++;
        }

        $result = implode(' ', $words);

        if ($cents > 0) {
            $result .= (empty($result) ? '' : ' Dollars and ') . $this->chunkToWords($cents) . ' Cents';
        } else {
            $result .= ' Dollars';
        }

        return trim($result);
    }

    protected function chunkToWords($chunk)
    {
        $below20 = [
            '',
            'One',
            'Two',
            'Three',
            'Four',
            'Five',
            'Six',
            'Seven',
            'Eight',
            'Nine',
            'Ten',
            'Eleven',
            'Twelve',
            'Thirteen',
            'Fourteen',
            'Fifteen',
            'Sixteen',
            'Seventeen',
            'Eighteen',
            'Nineteen'
        ];
        $tens = [
            '',
            '',
            'Twenty',
            'Thirty',
            'Forty',
            'Fifty',
            'Sixty',
            'Seventy',
            'Eighty',
            'Ninety'
        ];

        $words = [];

        $hundreds = floor($chunk / 100);
        $remainder = $chunk % 100;

        if ($hundreds > 0) {
            $words[] = $below20[$hundreds] . ' Hundred';
            if ($remainder > 0) {
                $words[] = 'and';
            }
        }

        if ($remainder > 0) {
            if ($remainder < 20) {
                $words[] = $below20[$remainder];
            } else {
                $words[] = $tens[floor($remainder / 10)];
                if ($remainder % 10 > 0) {
                    $words[] = $below20[$remainder % 10];
                }
            }
        }

        return implode(' ', array_filter($words));
    }
    function calculateTotalAllAmount($projectData)
    {
        $totalAllAmount = 0.0;

        if (isset($projectData['renovation_documents'])) {
            foreach ($projectData['renovation_documents'] as $document) {
                if (!is_null($document['signed_date'])) {
                    if (in_array($document['type'], ['QUOTATION', 'VARIATIONORDER'])) {
                        $totalAllAmount += (float)$document['total_amount'];
                    } elseif ($document['type'] == 'CANCELLATION') {
                        $totalAllAmount -= (float)$document['total_amount'];
                    }
                }
            }
        }

        return number_format($totalAllAmount, 2, '.', '');
    }

    public function downloadContractPdf(Request $request)
    {
        $projectId = $request->project_id;
        $contracts = collect((new FindAllContractQuery($projectId))->handle());
        $project = (new FindProjectByIdQuery($projectId))->handle();

        $folder_name  = env('COMPANY_FOLDER_NAME', 'Twp');
        $totalAllAmount = $this->calculateTotalAllAmount($project);

        $reno_data = (new FindSignedQuotationDocumentQuery($projectId))->handle();

        if(isset($reno_data->signed_date)){

            $date = $reno_data->signed_date;
        } else {
            
            $date = $project->contract->created_at;
        }

        $documentData = [
            "customer_name" => $project->contract->name,
            "nric" => $project->contract->nric,
            "company" => $project->contract->company,
            "address" => $project->contract->address,
            "created_date" => $date,
            "full_date" => Carbon::parse($date)->format('Y-m-d'),
            "ordinal_day" => $this->getDate($date),
            "date_by_month" => $this->getMonth($date),
            "pdpa_authorization" => $project->contract->pdpa_authorization
        ];
        $contractList = [
            "encode_owner_signature" => $contracts['encode_owner_signature'],
            "customer_name" => $contracts['customer_name'],
            "encode_contractor_signature" => $contracts['encode_contractor_signature'],
            "company_name" => $contracts['company_name'],
            "contractor_days" => $contracts['contractor_days'],
            "termination_days" => $contracts['termination_days'],
            "employer_witness_name" => $contracts['employer_witness_name'] == 'null' ? '' : $contracts['employer_witness_name'],
            "contractor_witness_name" => $contracts['contractor_witness_name'] == 'null' ? '' : $contracts['contractor_witness_name']
        ];
        $data = [
            "documentData" => $documentData,
            "contractLists" => $contractList,
            "folder_name" => $folder_name,
            "company_logo" => $this->getCompanyLogo($project->company->logo),
            "total_all_amount" => $totalAllAmount,
            "word_total_all_amount" => $this->numberToWords($totalAllAmount)
        ];

        $companyName = config('folder.company_folder_name');

        if($companyName == 'Tag' || $companyName == 'Tidplus'){
            $pdf = \PDF::loadView('pdf.TERM&CONDITION.term&condition', $data);
        } else {
            $pdf = \PDF::loadView('pdf.CONTRACT.contract', $data);
        }

        $pdf->setOption('encoding', 'utf-8');
        $pdfDocument = ContractEloquentModel::find($contracts['id']);
        $fileName = 'contract' . time() . '.pdf';
        $filePath = 'pdfs/' . $fileName;
        Storage::disk('public')->put($filePath, $pdf->output());
        // Store the file path in the database (assuming you have a model PdfDocument)
        if ($pdfDocument) {
            // Check if the old PDF file exists and delete it
            if (!empty($pdfDocument->pdf_file) && Storage::disk('public')->exists('pdfs/' . $pdfDocument->pdf_file)) {
                Storage::disk('public')->delete('pdfs/' . $pdfDocument->pdf_file);
            }
            // Update the database with the new file name
            $pdfDocument->update([
                'contract_pdf_file' => $fileName
            ]);
        }
        return $pdf->download("Contract.pdf");
    }
}
