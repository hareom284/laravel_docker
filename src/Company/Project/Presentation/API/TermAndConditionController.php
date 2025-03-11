<?php

namespace Src\Company\Project\Presentation\API;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\Document\Application\UseCases\Queries\FindContractByIdQuery;
use Src\Company\Document\Infrastructure\EloquentModels\RenovationDocumentsEloquentModel;
use Src\Company\Project\Application\Mappers\TermAndConditionMapper;
use Src\Company\Project\Application\Policies\TermAndConditionPolicy;
use Src\Company\Project\Application\UseCases\Commands\DeleteTermAndConditionCommand;
use Src\Company\Project\Application\UseCases\Commands\StoreTermAndConditionCommand;
use Src\Company\Project\Application\UseCases\Commands\UpdateCustomerSignatureCommand;
use Src\Company\Project\Application\UseCases\Commands\UpdateTermAndConditionCommand;
use Src\Company\Project\Application\UseCases\Queries\FindAllTermAndConditionQuery;
use Src\Company\Project\Application\UseCases\Queries\FindTermAndConditionByIdQuery;
use Src\Company\Project\Application\UseCases\Queries\FindAllTermAndConditionSelectQuery;
use Src\Company\Project\Application\UseCases\Queries\FindProjectByIdQuery;
use Src\Company\Project\Infrastructure\EloquentModels\ProjectEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\TermAndConditionEloquentModel;
use Src\Company\System\Infrastructure\EloquentModels\CompanyEloquentModel;

class TermAndConditionController extends Controller
{

    public function index(Request $request): JsonResponse
    {
        //check if user's has permission
        // abort_if(authorize('view', TermAndConditionPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Term And Condition!');

        try {

            $filters = $request->all();

            return response()->success((new FindAllTermAndConditionQuery($filters))->handle(), "Term And Condition List", Response::HTTP_OK);
        } catch (Exception $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function store(Request $request): JsonResponse
    {
        abort_if(authorize('store', TermAndConditionPolicy::class), Response::HTTP_FORBIDDEN, 'Need store permission for Term And Condition!');

        try {

            $termAndCondition = TermAndConditionMapper::fromRequest($request);

            $termAndConditionData = (new StoreTermAndConditionCommand($termAndCondition, $request->all()))->execute();

            return response()->success($termAndConditionData, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Exception $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function update(Request $request): JsonResponse
    {
        abort_if(authorize('update', TermAndConditionPolicy::class), Response::HTTP_FORBIDDEN, 'Need update permission for Term And Condition!');

        try {

            $termAndCondition = TermAndConditionMapper::fromRequest($request, $request->id);

            (new UpdateTermAndConditionCommand($termAndCondition, $request->all()))->execute();

            return response()->success(null, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Exception $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function show(int $id)
    {
        // abort_if(authorize('view', TermAndConditionPolicy::class), Response::HTTP_FORBIDDEN, 'Need update permission for Term And Condition!');

        try {

            return response()->success((new FindTermAndConditionByIdQuery($id))->handle(), "Term And Condition List", Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Exception $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        //check if user's has permission
        abort_if(authorize('destroy', TermAndConditionPolicy::class), Response::HTTP_FORBIDDEN, 'Need destroy permission for Term And Condition!');

        try {

            (new DeleteTermAndConditionCommand($id))->execute();

            return response()->success($id, "Deleted Successfully", Response::HTTP_OK);
        } catch (Exception $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function getAll(Request $request): JsonResponse
    {
        try {
            return response()->success((new FindAllTermAndConditionSelectQuery())->handle(), "Term And Condition List", Response::HTTP_OK);
        } catch (Exception $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function downloadPdf(int $id)
    {
        try {
            $termAndCondition = (new FindTermAndConditionByIdQuery($id))->handle();
            $current_folder_name = config('folder.company_folder_name');
            $printable = request('printable');
            $paymentTerms = [
                'payment_terms' => [
                    ['payment_percentage' => 30, 'payment_term' => 'Upon signing the contract'],
                    ['payment_percentage' => 50, 'payment_term' => 'Upon project midway'],
                    ['payment_percentage' => 20, 'payment_term' => 'Upon project completion'],
                ]
            ];

            $totalPrices = ['total_inclusive' => 10000];

            $data = [
                "termAndCondition" => collect($termAndCondition)->toArray(),
                "paymentTerms" => $paymentTerms,
                "totalPrices" => $totalPrices,
                "current_folder_name" => $current_folder_name,
            ];
            $company = CompanyEloquentModel::find(1);
            $headerFooterData = [
                "company_logo" => $this->getCompanyLogo($company->logo),
                "companies" => $company
            ];
            $pdf = \PDF::loadView('pdf.Sample.TermAndConditionSamplePdf', $data);
            $pdf->setOption('enable-javascript', true);
            if($current_folder_name == 'Tag'){
                $headerTagHtml = view('pdf.Common.Tag.termHeader', $headerFooterData)->render();
                $pdf->setOption('header-html', $headerTagHtml);
                $pdf->setOption('margin-top', 40);
                $pdf->setOption('margin-bottom', 6);
            } else if($current_folder_name == 'Tidplus') {
                $pdf->setOption('margin-top', 38);
                $pdf->setOption('margin-bottom', 29);
                $pdf->setOption('margin-left', 0);
                $pdf->setOption('margin-right', 0);
                $headerTidplusHtml = view('pdf.Common.Tidplus.termAndConditionHeader', $headerFooterData)->render();
                $footerTidplusHtml = view('pdf.Common.Tidplus.footer', $headerFooterData)->render();
                if($printable == 'true'){
                    $pdf->setOption('header-html', '');
                    $pdf->setOption('header-html', '');
                } else {
                    $pdf->setOption('footer-html', $footerTidplusHtml);
                    $pdf->setOption('header-html', $headerTidplusHtml);
                }
            }

            // Find the contract record
            $pdfDocument = TermAndConditionEloquentModel::find($id);

            if ($pdfDocument) {
                // Delete the old media file if it exists
                $pdfDocument->clearMediaCollection('termAndConditionPdf');

                // Save the new PDF to the media library
                $pdfDocument
                    ->addMediaFromString($pdf->output())
                    ->usingFileName('termAndConditionPdf' . time() . '.pdf')
                    ->toMediaCollection('termAndConditionPdf', 'media_termAndCondition_pdf');
            }

            // Return the PDF for download
            return $pdf->download("TermAndConditionSample.pdf");
        } catch (\Exception $e) {
            return response()->error($e->getMessage());
        }
    }

    public function customerSign(Request $request)
    {
        try {
            $signaturesData = $request->input('signaturesData');
            $files = $request->file('signaturesData');
            $contract_id = $request->contract_id;
            $termAndConditionData = (new UpdateCustomerSignatureCommand($contract_id, $signaturesData, $files))->execute();

            return response()->success($termAndConditionData, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Exception $e) {

            return response()->error($e->getMessage());
        }
    }

    public function downloadSignedPdf(int $id)
    {
        $contract = (new FindContractByIdQuery($id))->handle();
        $contractData = collect($contract)->toArray();
        $project = $contractData['project'];
        $signedSignatures = $contractData['term_and_condition_signatures'];
        $termAndConditionId = $project['term_and_condition_id'] ?? null;
        $termAndCondition = (new FindTermAndConditionByIdQuery($termAndConditionId))->handle();
        $termAndConditionData = collect($termAndCondition)->toArray();
        $projectData = (new FindProjectByIdQuery($project['id']))->handle();
        $projectDataArray = collect($projectData)->toArray();
        $totalAllAmount = $this->calculateTotalAllAmount($projectDataArray);
        $current_folder_name = config('folder.company_folder_name');
        $printable = request('printable');

        $customer_names = $this->getCustomerNames($projectDataArray);
        $project_address = $this->getProjectAddress($projectDataArray);

        $updatedTermAndConditionData = json_decode(json_encode($termAndConditionData), true);

        foreach ($updatedTermAndConditionData['contents'] as &$content) {
            foreach ($content['paragraphs'] as &$paragraph) {
                $matchingData = collect($signedSignatures)->firstWhere('term_and_condition_paragraph_id', $paragraph['id']);

                if ($matchingData) {
                    $paragraph['customer_signatures_with_data'] = $matchingData['customer_signatures_with_data'];
                } else {
                    $paragraph['customer_signatures_with_data'] = null;
                }
            }
        }

        $sign_quotation = RenovationDocumentsEloquentModel::where('project_id', $project['id'])
            ->where('type', 'QUOTATION')
            ->whereNotNull('signed_date')
            ->pluck('payment_terms')
            ->first();
        $paymentTerms = null;
        if (isset($sign_quotation)) {
            $sign_quotation_data = json_decode($sign_quotation);
            if (isset($sign_quotation_data)) {
                $paymentTerms = $sign_quotation_data->payment_terms;
            }
        }

        $data = [
            "termAndCondition" => $updatedTermAndConditionData,
            "paymentTerms" => $paymentTerms,
            "totalPrices" => $totalAllAmount,
            "totalPricesInWord" => $this->numberToWords($totalAllAmount),
            "customer_names" => $customer_names,
            "project_address" => $project_address,
            "current_folder_name" => $current_folder_name,
            "contract_agreement_number" => $project['agreement_no'] ?? ''
        ];
        $headerFooterData = [
            "company_logo" => $this->getCompanyLogo($projectData['company']['logo']),
            "companies" => $projectData['company']
        ];
        $pdf = \PDF::loadView('pdf.TERM&CONDITION.newTermAndConditionPdf', $data);
        $pdf->setOption('enable-javascript', true);
        if($current_folder_name == 'Tag'){
            $headerTagHtml = view('pdf.Common.Tag.termHeader', $headerFooterData)->render();
            $pdf->setOption('header-html', $headerTagHtml);
            $pdf->setOption('margin-top', 40);
            $pdf->setOption('margin-bottom', 6);
        } else if($current_folder_name == 'Tidplus') {
            $pdf->setOption('margin-top', 38);
            $pdf->setOption('margin-bottom', 29);
            $pdf->setOption('margin-left', 0);
            $pdf->setOption('margin-right', 0);
            $headerTidplusHtml = view('pdf.Common.Tidplus.termAndConditionHeader', $headerFooterData)->render();
            $footerTidplusHtml = view('pdf.Common.Tidplus.footer', $headerFooterData)->render();
            if($printable == 'true'){
                $pdf->setOption('header-html', '');
                $pdf->setOption('header-html', '');
            } else {
                $pdf->setOption('footer-html', $footerTidplusHtml);
                $pdf->setOption('header-html', $headerTidplusHtml);
            }
        }

        // Find the contract record
        $pdfDocument = TermAndConditionEloquentModel::find($id);

        if ($pdfDocument) {
            // Delete the old media file if it exists
            $pdfDocument->clearMediaCollection('termAndConditionPdf');

            // Save the new PDF to the media library
            $pdfDocument
                ->addMediaFromString($pdf->output())
                ->usingFileName('termAndConditionPdf' . time() . '.pdf')
                ->toMediaCollection('termAndConditionPdf', 'media_termAndCondition_pdf');
        }

        // Return the PDF for download
        return $pdf->download("TermAndConditionSample.pdf");
    }

    public function downloadUnSignPdf(int $project_id)
    {
        $project = ProjectEloquentModel::find($project_id);
        $termAndConditionId = $project->term_and_condition_id ?? null;
        $termAndCondition = (new FindTermAndConditionByIdQuery($termAndConditionId))->handle();
        $termAndConditionData = collect($termAndCondition)->toArray();
        $projectData = (new FindProjectByIdQuery($project['id']))->handle();
        $projectDataArray = collect($projectData)->toArray();
        $totalAllAmount = $this->calculateTotalAllAmount($projectDataArray);
        $current_folder_name = config('folder.company_folder_name');
        $printable = request('printable');

        $customer_names = $this->getCustomerNames($projectDataArray);
        $project_address = $this->getProjectAddress($projectDataArray);

        $updatedTermAndConditionData = json_decode(json_encode($termAndConditionData), true);

        $sign_quotation = RenovationDocumentsEloquentModel::where('project_id', $project['id'])
            ->where('type', 'QUOTATION')
            ->orderBy('created_at', 'desc')
            ->pluck('payment_terms')
            ->first();
        $paymentTerms = null;
        if (isset($sign_quotation)) {
            $sign_quotation_data = json_decode($sign_quotation);
            if (isset($sign_quotation_data)) {
                $paymentTerms = $sign_quotation_data->payment_terms;
            }
        }

        $data = [
            "termAndCondition" => $updatedTermAndConditionData,
            "paymentTerms" => $paymentTerms,
            "totalPrices" => $totalAllAmount,
            "totalPricesInWord" => $this->numberToWords($totalAllAmount),
            "customer_names" => $customer_names,
            "project_address" => $project_address,
            "current_folder_name" => $current_folder_name,
            "contract_agreement_number" => $project['agreement_no'] ?? ''
        ];
        $headerFooterData = [
            "company_logo" => $this->getCompanyLogo($projectData['company']['logo']),
            "companies" => $projectData['company']
        ];
        $pdf = \PDF::loadView('pdf.TERM&CONDITION.unsignTermAndConditionPdf', $data);
        $pdf->setOption('enable-javascript', true);
        if($current_folder_name == 'Tag'){
            $headerTagHtml = view('pdf.Common.Tag.termHeader', $headerFooterData)->render();
            $pdf->setOption('header-html', $headerTagHtml);
            $pdf->setOption('margin-top', 40);
            $pdf->setOption('margin-bottom', 6);
        } else if($current_folder_name == 'Tidplus') {
            $pdf->setOption('margin-top', 38);
            $pdf->setOption('margin-bottom', 29);
            $pdf->setOption('margin-left', 0);
            $pdf->setOption('margin-right', 0);
            $headerTidplusHtml = view('pdf.Common.Tidplus.termAndConditionHeader', $headerFooterData)->render();
            $footerTidplusHtml = view('pdf.Common.Tidplus.footer', $headerFooterData)->render();
            if($printable == 'true'){
                $pdf->setOption('header-html', '');
                $pdf->setOption('header-html', '');
            } else {
                $pdf->setOption('footer-html', $footerTidplusHtml);
                $pdf->setOption('header-html', $headerTidplusHtml);
            }
        }

        // Find the contract record
        $pdfDocument = TermAndConditionEloquentModel::find($termAndConditionId);

        if ($pdfDocument) {
            // Delete the old media file if it exists
            $pdfDocument->clearMediaCollection('termAndConditionPdf');

            // Save the new PDF to the media library
            $pdfDocument
                ->addMediaFromString($pdf->output())
                ->usingFileName('termAndConditionPdf' . time() . '.pdf')
                ->toMediaCollection('termAndConditionPdf', 'media_termAndCondition_pdf');
        }

        // Return the PDF for download
        return $pdf->download("TermAndConditionSample.pdf");
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

    public function chunkToWords($chunk)
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

    public function getCustomerNames($projectData)
    {
        $customers = $projectData['customers_array'] ?? [];
        
        $customerNames = array_map(function ($customer) {
            $namePrefix = $customer['name_prefix'] ?? '';
            $firstName = $customer['first_name'] ?? '';
            $lastName = $customer['last_name'] ?? '';

            return trim("{$namePrefix} {$lastName} {$firstName}");
        }, $customers);

        return implode(', ', array_filter($customerNames)); // Join names with commas and remove empty entries
    }

    /**
     * Get formatted project address from project data.
     *
     * @param array $projectData
     * @return string
     */
    public function getProjectAddress($projectData)
    {
        $property = $projectData['properties'] ?? null;

        if (!$property) {
            return '';
        }

        $blockNum = $property->block_num ?? '';
        $streetName = $property->street_name ?? '';
        $unitNum = $property->unit_num ?? null;
        $postalCode = $property->postal_code ? 'S('.$property->postal_code.')' : '';

        // Construct the address
        $address = "{$blockNum} {$streetName}";
        if ($unitNum) {
            $address .= " #{$unitNum}";
        }
        $address .= " {$postalCode}";

        return trim($address);
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
}
