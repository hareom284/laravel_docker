<?php

namespace Src\Company\Document\Presentation\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Src\Common\Infrastructure\Laravel\Controller;
use Symfony\Component\HttpFoundation\Response;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Src\Company\Document\Application\Mappers\RenovationDocumentsMapper;
use Src\Company\Document\Application\Policies\ProjectQuotationPolicy;
use Src\Company\Document\Application\UseCases\Queries\FindAllVOQuery;
use Src\Company\Document\Application\UseCases\Queries\FindVariationItemsQuery;
use Src\Company\Document\Application\UseCases\Queries\GetCountListVOQuery;
use Src\Company\Document\Application\Policies\VOPolicy;
use Src\Company\Document\Application\Requests\StoreProjectQuotationRequest;
use Src\Company\Document\Application\UseCases\Commands\StoreVariationDocumentCommand;
use Src\Company\Document\Application\UseCases\Queries\FindVariationUpdateItemsQuery;

class VariationOrderController extends Controller
{

    public function countLists($project_id)
    {
        abort_if(authorize('view', VOPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Variation Order!');

        try {

            $result = (new GetCountListVOQuery($project_id))->handle();

            return response()->json($result, Response::HTTP_OK);
        } catch (\DomainException $domainException) {
            return response()->json(['error' => $domainException->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function index($project_id)
    {
        abort_if(authorize('view', VOPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Variation Order!');

        $result = (new FindAllVOQuery($project_id))->handle();

        return response()->json($result, Response::HTTP_OK);
    }

    public function getAllVariationOrderItems(Request $request, $project_id)
    {
        abort_if(authorize('view', VOPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Variation Order!');

        try {

            $saleperson_id = $request->saleperson_id ?? null;

            $variation_items = (new FindVariationItemsQuery($project_id, $saleperson_id))->handle();

            return response()->json($variation_items, Response::HTTP_OK);
        } catch (\DomainException $domainException) {
            return response()->json(['error' => $domainException->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function getUpdateAllVariationOrderItems(Request $request, $document_id)
    {
        abort_if(authorize('view', VOPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Variation Order!');

        try {

            $saleperson_id = $request->saleperson_id ?? null;

            $variation_items = (new FindVariationUpdateItemsQuery($document_id, $saleperson_id))->handle();

            return response()->json($variation_items, Response::HTTP_OK);
        } catch (\DomainException $domainException) {
            return response()->json(['error' => $domainException->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function store(StoreProjectQuotationRequest $request): JsonResponse
    {
        //check if user's has permission
        abort_if(authorize('store', ProjectQuotationPolicy::class), Response::HTTP_FORBIDDEN, 'Need store permission for Renovation Document!');

        try {
            $data = json_decode($request->data);
            $renovation_documents = RenovationDocumentsMapper::fromRequest($request, $request->reno_document_id);  //new change - add reno document for document update
            $renovation_documents_data = (new StoreVariationDocumentCommand($renovation_documents, $data))->execute();

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
