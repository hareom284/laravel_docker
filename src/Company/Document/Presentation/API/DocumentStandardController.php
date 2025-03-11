<?php

namespace Src\Company\Document\Presentation\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\Document\Domain\Repositories\DocumentStandardRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Src\Company\Document\Application\Mappers\DocumentStandardMapper;
use Src\Company\Document\Application\Requests\StoreDocumentStandardRequest;
use Src\Company\Document\Application\UseCases\Commands\DeleteDocumentStandardCommand;
use Src\Company\Document\Application\UseCases\Commands\StoreDocumentStandardCommand;
use Src\Company\Document\Application\UseCases\Commands\UpdateDocumentStandardCommand;
use Src\Company\Document\Application\UseCases\Queries\FindDocumentStandardByCompanyIdQuery;
use Src\Company\Document\Application\UseCases\Queries\FindDocumentStandardByIdQuery;
use Src\Company\Document\Application\Policies\DocumentStandardPolicy;

class DocumentStandardController extends Controller
{
    private $documentstandardInterFace;

    public function __construct(DocumentStandardRepositoryInterface $documentstandardRepository)
    {
        $this->documentstandardInterFace = $documentstandardRepository;
    }

    public function index(Request $request): JsonResponse
    {
        abort_if(authorize('view', DocumentStandardPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Document Standard!');

        try {

            $filters = $request;

            $documents = $this->documentstandardInterFace->getDocumentStandards($filters);

            return response()->success($documents, 'success', Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function show(int $id): JsonResponse
    {
        abort_if(authorize('view', DocumentStandardPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Document Standard!');

        try {
            $documentStandard = (new FindDocumentStandardByIdQuery($id))->handle();

            return response()->success($documentStandard, "Success show data.", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function findDataByCompanyId(int $id): JsonResponse
    {
        abort_if(authorize('view', DocumentStandardPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Document Standard!');

        try {
            $documentStandard = (new FindDocumentStandardByCompanyIdQuery($id))->handle();

            return response()->success($documentStandard, "Success show data.", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function store(Request $request): JsonResponse
    {
        abort_if(authorize('store', DocumentStandardPolicy::class), Response::HTTP_FORBIDDEN, 'Need store permission for Document Standard!');

        try {

            $array = $request->all();

            foreach ($array as $data) {

                $headerFooter = json_decode(json_encode($data));

                $documentStandard = DocumentStandardMapper::fromRequest($headerFooter, $headerFooter->id);

                (new StoreDocumentStandardCommand($documentStandard))->execute();
            }

            return response()->success(null, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function update(int $id, Request $request): JsonResponse
    {

        abort_if(authorize('update', DocumentStandardPolicy::class), Response::HTTP_FORBIDDEN, 'Need update permission for Document Standard!');

        try {
            $documentStandard = DocumentStandardMapper::fromRequest($request, $id);

            (new UpdateDocumentStandardCommand($documentStandard))->execute();

            return response()->success($documentStandard, 'success', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        abort_if(authorize('destroy', DocumentStandardPolicy::class), Response::HTTP_FORBIDDEN, 'Need destroy permission for Document Standard!');

        try {
            (new DeleteDocumentStandardCommand($id))->execute();

            return response()->success($id, "Successfully Deleted", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }
}
