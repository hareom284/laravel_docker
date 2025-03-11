<?php

namespace Src\Company\CompanyManagement\Presentation\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Symfony\Component\HttpFoundation\Response;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\CompanyManagemen\Application\Requests\StoreCustomerPaymentRequest;
use Src\Company\CompanyManagement\Application\Requests\SyncDataRequest;
use Src\Company\CompanyManagement\Application\UseCases\Commands\SyncDataWithAccountingSoftwareCommand;
use Src\Company\CompanyManagement\Application\UseCases\Queries\FindInProgressProjectsQuery;
use Src\Company\CompanyManagement\Application\UseCases\Queries\FindProjectForAccountantQuery;
use Src\Company\CompanyManagement\Application\UseCases\Queries\GetAllBankInfosQuery;
use Src\Company\CompanyManagement\Application\UseCases\Queries\GetAllQuickBookExpensesQuery;
use Src\Company\Document\Domain\Repositories\ContractRepositoryInterface;
use Src\Company\Document\Domain\Repositories\EvoRepositoryInterface;
use Src\Company\Document\Domain\Repositories\RenovationDocumentInterface;
use Src\Company\Project\Domain\Repositories\ProjectRepositoryInterface;

class AccountantController extends Controller
{
    private $projectInterface;
    private $contractRepositoryInterface;
    private $renovationDocumentInterface;
    private $evoRepositoryInterface;

    public function __construct(
        ProjectRepositoryInterface $projectRepository,
        ContractRepositoryInterface $contractRepositoryInterface,
        RenovationDocumentInterface $renovationDocumentInterface,
        EvoRepositoryInterface $evoRepositoryInterface
    ){
        $this->projectInterface = $projectRepository;
        $this->contractRepositoryInterface = $contractRepositoryInterface;
        $this->renovationDocumentInterface = $renovationDocumentInterface;
        $this->evoRepositoryInterface = $evoRepositoryInterface;
    }

    public function getProjectLists(Request $request): JsonResponse
    {
        try {

            $perPage = $request->perPage;
            $salePerson = $request->salePerson ?? 0;
            $filterText = $request->filterText ?? '';
            $status = $request->status ?? '';
            $created_at = $request->created_at ?? '';
            $projects = (new FindProjectForAccountantQuery($perPage,$salePerson,$filterText, $status, $created_at))->handle();

            return response()->success($projects,'success',Response::HTTP_OK);

        } catch (UnauthorizedUserException $e) {

            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }

    }

    public function getInProgressProjectLists(): JsonResponse
    {
        try {

            $projects = (new FindInProgressProjectsQuery())->handle();

            return response()->success($projects,'success',Response::HTTP_OK);

        } catch (UnauthorizedUserException $e) {

            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }

    }

    public function getSaleConfirmAmt($projectId): JsonResponse
    {
        try { 
            // Initialize the data array
            $data = [];

            $evoAmts = $this->evoRepositoryInterface->getEvoAmt($projectId);

            if (count($evoAmts) !== 0) {

                foreach ($evoAmts as $index => $evoAmt) {
                    $data[] = [
                        'name' => "EVO" . $index + 1,
                        'version' => $evoAmt->projects->agreement_no.'/EVO'.$evoAmt->version_number,
                        'total_amount' => number_format($evoAmt->grand_total, 2, '.', ','),
                        'total_amount_without_format' => $evoAmt->grand_total,
                        'signed_date' => $evoAmt->signed_date
                    ];
                }
            }

            $modifiedDocs = $this->renovationDocumentInterface->getConfirmAmtsByProjectId($projectId);
            
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

    public function getAllBankInfos()
    {
        try {

            $bankInfos = (new GetAllBankInfosQuery())->handle();

            return response()->success($bankInfos,'success',Response::HTTP_OK);
            
        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function getAllQuickBookExpenses()
    {
        try {

            $bankInfos = (new GetAllQuickBookExpensesQuery())->handle();

            return response()->success($bankInfos,'success',Response::HTTP_OK);
            
        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function syncDataWithAccountingSoftware(SyncDataRequest $request)
    {
        try {

            $entity = $request->syncEntity;
            $companyId = $request->syncCompany;

            $responseData = (new SyncDataWithAccountingSoftwareCommand($entity,$companyId))->execute();

            return response()->success($responseData,'success',Response::HTTP_OK);
            
        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

}