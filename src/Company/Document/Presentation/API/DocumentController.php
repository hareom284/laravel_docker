<?php

namespace Src\Company\Document\Presentation\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Src\Company\Document\Application\Mappers\DocumentMapper;
use Src\Company\Document\Application\UseCases\Commands\StoreDocumentCommand;
use Src\Company\Document\Application\UseCases\Commands\DeleteDocumentCommand;
use Src\Company\Document\Application\UseCases\Commands\UpdateDocumentCommand;
use Src\Company\Document\Application\UseCases\Queries\FindDocumentByIdQuery;
use Symfony\Component\HttpFoundation\Response;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\Document\Application\Requests\StoreDocumentRequest;
use Src\Company\Document\Application\UseCases\Queries\FindDocumentsByProjectId;
use Src\Company\Document\Domain\Repositories\DocumentRepositoryInterface;
use Src\Company\Document\Application\Policies\DocumentPolicy;


class DocumentController extends Controller
{
    private $documentInterFace;

    public function __construct(DocumentRepositoryInterface $documentRepository)
    {
        $this->documentInterFace = $documentRepository;
    }

    public function index(Request $request): JsonResponse
    {
        abort_if(authorize('view', DocumentPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Document!');

        try {

            $filters = $request;

            $documents = $this->documentInterFace->getDocuments($filters);

            return response()->success($documents, 'success', Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function documentListByProjectId($projectId)
    {
        abort_if(authorize('view', DocumentPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Document!');

        try {

            $documentLists = (new FindDocumentsByProjectId($projectId))->handle();

            return response()->success($documentLists, 'success', Response::HTTP_CREATED);
        } catch (UnauthorizedUserException $e) {

            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function show(int $id): JsonResponse
    {
        abort_if(authorize('view', DocumentPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Document!');

        try {

            $document = (new FindDocumentByIdQuery($id))->handle();

            return response()->success($document, 'success', Response::HTTP_CREATED);
        } catch (UnauthorizedUserException $e) {

            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function store(StoreDocumentRequest $request): JsonResponse
    {
        abort_if(authorize('store', DocumentPolicy::class), Response::HTTP_FORBIDDEN, 'Need store permission for Document!');

        try {
            $document = DocumentMapper::fromRequest($request);

            $documentData = (new StoreDocumentCommand($document))->execute();

            return response()->success($documentData, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function update(int $id, Request $request): JsonResponse
    {
        abort_if(authorize('update', DocumentPolicy::class), Response::HTTP_FORBIDDEN, 'Need update permission for Document!');

        try {
            $document = DocumentMapper::fromRequest($request, $id);

            (new UpdateDocumentCommand($document))->execute();

            return response()->success($document, 'success', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        abort_if(authorize('destroy', DocumentPolicy::class), Response::HTTP_FORBIDDEN, 'Need destroy permission for Document!');

        try {
            (new DeleteDocumentCommand($id))->execute();

            return response()->success($id, "Successfully Deleted", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }
}
