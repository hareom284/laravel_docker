<?php

namespace Src\Company\Document\Presentation\API;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\Document\Application\Policies\VOPolicy;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Src\Company\Document\Application\Policies\ProjectQuotationPolicy;
use Src\Company\Document\Application\Mappers\RenovationDocumentsMapper;
use Src\Company\Document\Application\UseCases\Queries\GetCountListVOQuery;
use Src\Company\Document\Application\Requests\StoreProjectQuotationRequest;
use Src\Company\Document\Application\UseCases\Queries\FindVariationItemsQuery;
use Src\Company\Document\Application\UseCases\Queries\FindVariationItemsMobileQuery;
use Src\Company\Document\Application\UseCases\Queries\FindVariationUpdateItemsQuery;
use Src\Company\Document\Application\UseCases\Commands\StoreVariationDocumentMobileCommand;


class VariationOrderMobileController extends Controller
{


    //if the variation order is empty get items for the first time
    public function getVariationOrderItems(Request $request, $project_id)
    {
        try {

            $saleperson_id = $request->saleperson_id ?? null;

            $variation_items = (new FindVariationItemsMobileQuery($project_id, $saleperson_id))->handle();

            return response()->json($variation_items, Response::HTTP_OK);

        } catch (\Exception $domainException) {
            return response()->json(['error' => $domainException->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    // variation order update api 
    public function getUpdateVariationOrderItems(Request $request, $document_id)
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

    // save variation order items and signed during submit saleperson sign
    public function store(StoreProjectQuotationRequest $request): JsonResponse
    {
        //check if user's has permission
        abort_if(authorize('store', ProjectQuotationPolicy::class), Response::HTTP_FORBIDDEN, 'Need store permission for Renovation Document!');

        try {

            DB::beginTransaction();
            $data = json_decode($request->data);
            $renovation_documents = RenovationDocumentsMapper::fromRequest($request, $request->reno_document_id);  //new change - add reno document for document update
            $renovation_documents_data = (new StoreVariationDocumentMobileCommand($renovation_documents, $data))->execute();

            $request['renovation_document_id'] = $renovation_documents_data->id;
            $request['project_id'] = $request->project_id;
            $request['pdf_status'] = "SAVE";
            $request['type'] = $request->type;

            // $this->downloadPdf($request);
            $reno_controller = new RenovationDocumentController;
            $reno_controller->downloadPdf($request);

            DB::commit();

            return response()->json($renovation_documents_data, Response::HTTP_CREATED);
        } catch (\Exception $error) {

            DB::rollback();

            return response()->json(['error' => $error->getTrace()], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }
}
