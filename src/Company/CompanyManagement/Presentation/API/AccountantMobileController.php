<?php

namespace Src\Company\CompanyManagement\Presentation\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Symfony\Component\HttpFoundation\Response;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\CompanyManagemen\Application\Requests\StoreCustomerPaymentRequest;
use Src\Company\CompanyManagement\Application\UseCases\Queries\FindInProgressProjectsQuery;
use Src\Company\CompanyManagement\Application\UseCases\Queries\FindProjectForAccountantQuery;
use Src\Company\CompanyManagement\Application\UseCases\Queries\GetAllBankInfosQuery;
use Src\Company\CompanyManagement\Application\UseCases\Queries\GetAllQuickBookExpensesQuery;
use Src\Company\Document\Application\UseCases\Queries\GetConfirmAmtsByProjectIdMobileQuery;
use Src\Company\Document\Application\UseCases\Queries\GetEvoAmountMobileQuery;
use Src\Company\Document\Domain\Repositories\ContractRepositoryInterface;
use Src\Company\Document\Domain\Repositories\EvoRepositoryInterface;
use Src\Company\Document\Domain\Repositories\RenovationDocumentInterface;
use Src\Company\Project\Domain\Repositories\ProjectRepositoryInterface;

class AccountantMobileController extends Controller
{

    public function getSaleConfirmAmt($projectId): JsonResponse
    {
        try { 
            
        // $amounts = [
        //     [
        //         "name" => "Contract",
        //         "version" => "DD/SO/1124/148/40",
        //         "signed_date" => "2024-11-22",
        //         "total_amount" => "365.15"
        //     ],
        //     [
        //         "name" => "VARIATIONORDER",
        //         "version" => "DD/SO/1124/148/40/VO1",
        //         "signed_date" => "2024-11-22",
        //         "total_amount" => "7,150.40"
        //     ],
        //     [
        //         "name" => "VARIATIONORDER 1",
        //         "version" => "DD/SO/1124/148/40/VO2",
        //         "signed_date" => "2024-11-22",
        //         "total_amount" => "-365.15"
        //     ]
        // ];

        $data = [];

        $evoAmts = (new GetEvoAmountMobileQuery($projectId))->handle();

        if (count($evoAmts) !== 0) {

            foreach ($evoAmts as $index => $evoAmt) {
                $data[] = [
                    'name' => "EVO" . $index + 1,
                    'version' => $evoAmt->projects->agreement_no.'/EVO'.$evoAmt->version_number,
                    'total_amount' => $evoAmt->total_amount,
                    'signed_date' => $evoAmt->signed_date
                ];
            }
        }

        $modifiedDocs = (new GetConfirmAmtsByProjectIdMobileQuery($projectId))->handle();
        
        // Add the modifiedDocs to the data array
        if($modifiedDocs){

            foreach ($modifiedDocs as $doc) {

                if($doc['name'] === "QUOTATION"){

                    $data[] = [
                        'name' => "Contract",
                        'version' => $doc['version'],
                        'signed_date' => $doc['signed_date'],
                        'total_amount' => $doc['total_amount']
                    ];

                }else{

                    $data[] = [
                        'name' => $doc['name'],
                        'version' => $doc['version'],
                        'signed_date' => $doc['signed_date'],
                        'total_amount' => $doc['total_amount']
                    ];
                }
                
            }

        }
        
        $results['amounts'] = $data;
        
        return response()->success($results, 'success', Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

}