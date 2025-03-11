<?php

namespace Src\Company\Document\Presentation\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\Document\Application\Mappers\HDBFormsMapper;
use Src\Company\Document\Application\Requests\StoreHDBFormsRequest;
use Src\Company\Document\Application\UseCases\Commands\DeleteHDBFormsCommand;
use Src\Company\Document\Application\UseCases\Commands\StoreHDBFormsCommand;
use Src\Company\Document\Application\UseCases\Commands\UpdateHDBFormsCommand;
use Src\Company\Document\Application\UseCases\Queries\FindHDBFormsByIdQuery;
use Src\Company\Document\Domain\Repositories\HDBFormsRepositoryInterface;
use Src\Company\Document\Application\Policies\HDBPolicy;


class HDBFormsController extends Controller
{
    private $hdbFormsInterFace;

    public function __construct(HDBFormsRepositoryInterface $hdbFormsRepository)
    {
        $this->hdbFormsInterFace = $hdbFormsRepository;
    }

    public function index($project_id): JsonResponse
    {
        abort_if(authorize('view', HDBPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for HDB Form!');

        try {

            $hdbForms = $this->hdbFormsInterFace->getHDBForms($project_id);

            return response()->success($hdbForms, 'success', Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function show(int $id): JsonResponse
    {
        abort_if(authorize('view', HDBPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for HDB Form!');

        try {
            return response()->json((new FindHDBFormsByIdQuery($id))->handle());
        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function store(StoreHDBFormsRequest $request): JsonResponse
    {
        abort_if(authorize('store', HDBPolicy::class), Response::HTTP_FORBIDDEN, 'Need store permission for HDB Form!');

        try {

            $hdbForms = HDBFormsMapper::fromRequest($request);

            $hdbFormsData = (new StoreHDBFormsCommand($hdbForms))->execute();

            return response()->success($hdbFormsData, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function update(int $id, Request $request): JsonResponse
    {
        abort_if(authorize('update', HDBPolicy::class), Response::HTTP_FORBIDDEN, 'Need update permission for HDB Form!');

        try {
            $hdbForms = HDBFormsMapper::fromRequest($request, $id);

            (new UpdateHDBFormsCommand($hdbForms))->execute();

            return response()->success($hdbForms, 'success', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        abort_if(authorize('destroy', HDBPolicy::class), Response::HTTP_FORBIDDEN, 'Need destroy permission for HDB Form!');

        try {
            (new DeleteHDBFormsCommand($id))->execute();

            return response()->success($id, "Successfully Deleted", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }
}
