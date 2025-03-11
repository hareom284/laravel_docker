<?php

namespace Src\Company\Document\Presentation\API;

use Src\Common\Infrastructure\Laravel\Controller;
use Symfony\Component\HttpFoundation\Response;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Illuminate\Http\JsonResponse;
use Src\Company\Document\Application\Mappers\RenovationDocumentsMapper;
use Src\Company\Document\Application\Policies\ProjectQuotationPolicy;
use Src\Company\Document\Application\UseCases\Queries\FindCancellationItemsQuery;
use Src\Company\Document\Application\UseCases\Queries\GetCountListsCNQuery;
use Src\Company\Document\Application\Requests\StoreProjectQuotationRequest;
use Src\Company\Document\Application\UseCases\Commands\StoreCancellationDocumentCommand;

class CancellationController extends Controller
{
    public function getAllCancellationItems($projectId): JsonResponse
    {
        try {

            $cancellation_items = (new FindCancellationItemsQuery($projectId))->handle();

            return response()->json($cancellation_items, Response::HTTP_OK);
        } catch (\DomainException $domainException) {
            return response()->json(['error' => $domainException->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function countLists($projectId)
    {
        try {

            $result = (new GetCountListsCNQuery($projectId))->handle();

            return response()->json($result, Response::HTTP_OK);
        } catch (\DomainException $domainException) {
            return response()->json(['error' => $domainException->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function store (StoreProjectQuotationRequest $request): JsonResponse
    {

        //check if user's has permission
        abort_if(authorize('store', ProjectQuotationPolicy::class), Response::HTTP_FORBIDDEN, 'Need store permission for Renovation Document!');

        try {
            $data = json_decode($request->data);

            $renovation_documents = RenovationDocumentsMapper::fromRequest($request, $request->reno_document_id);  //new change - add reno document for document update
            $renovation_documents_data = (new StoreCancellationDocumentCommand($renovation_documents, $data))->execute();

            $request['renovation_document_id'] = $renovation_documents_data->id;
            $request['project_id'] = $request->project_id;
            $request['pdf_status'] = "SAVE";
            $request['type'] = $request->type;

            // $this->downloadPdf($request);
            $reno_controller = new RenovationDocumentController;
            $reno_controller->downloadPdf($request);

            return response()->json($renovation_documents_data, Response::HTTP_CREATED);
        } catch (\DomainException $domainException) {
            return response()->json(['error' => $domainException->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }
}
