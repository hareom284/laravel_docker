<?php

namespace Src\Company\Document\Presentation\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\Document\Application\Mappers\ElectricalPlansMapper;
use Src\Company\Document\Application\Requests\StoreElectricalPlansRequest;
use Src\Company\Document\Application\UseCases\Commands\DeleteElectricalPlansCommand;
use Src\Company\Document\Application\UseCases\Commands\StoreElectricalPlansCommand;
use Src\Company\Document\Domain\Repositories\ElectricalPlansRepositoryInterface;

class ElectricalPlansController extends Controller
{
    private $electricalPlansInterFace;

    public function __construct(ElectricalPlansRepositoryInterface $electricalPlansRepository)
    {
        $this->electricalPlansInterFace = $electricalPlansRepository;
    }

    public function index($project_id): JsonResponse
    {
        try {

            $electricalPlans = $this->electricalPlansInterFace->getElectricalPlans($project_id);

            return response()->success($electricalPlans,'success',Response::HTTP_OK);

        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    // public function show(int $id): JsonResponse
    // {
    //     try {
    //         return response()->json((new FindHDBFormsByIdQuery($id))->handle());
    //     } catch (UnauthorizedUserException $e) {
    //         return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
    //     }
    // }

    public function store(StoreElectricalPlansRequest $request): JsonResponse
    {   
        try {

            $electricalPlans = ElectricalPlansMapper::fromRequest($request);

            $electricalFormsData = (new StoreElectricalPlansCommand($electricalPlans, $request->salesperson_id, $request->materials))->execute();

            return response()->success($electricalFormsData, 'success', Response::HTTP_CREATED);

        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);

        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    // public function update(int $id, Request $request): JsonResponse
    // { 
    //     try {
    //         $hdbForms = HDBFormsMapper::fromRequest($request, $id);

    //         (new UpdateHDBFormsCommand($hdbForms))->execute();

    //         return response()->success($hdbForms,'success',Response::HTTP_OK);

    //     } catch (\DomainException $e) {

    //         return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);

    //     } catch (UnauthorizedUserException $e) {

    //         return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
    //     }
    // }

    public function destroy(int $id): JsonResponse
    {
        try {
            (new DeleteElectricalPlansCommand($id))->execute();

            return response()->success($id,"Successfully Deleted", Response::HTTP_OK);

        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }
}