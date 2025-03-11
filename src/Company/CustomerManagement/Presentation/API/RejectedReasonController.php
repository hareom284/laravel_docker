<?php

namespace Src\Company\CustomerManagement\Presentation\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Symfony\Component\HttpFoundation\Response;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\CustomerManagement\Application\Mappers\RejectedReasonMapper;
use Src\Company\CustomerManagement\Application\UseCases\Commands\DeleteRejectedReasonCommand;
use Src\Company\CustomerManagement\Application\UseCases\Commands\StoreRejectedReasonsCommand;
use Src\Company\CustomerManagement\Application\UseCases\Commands\UpdateRejectedReasonCommand;
use Src\Company\CustomerManagement\Application\UseCases\Commands\UpdateRejectedReasonOrderCommand;
use Src\Company\CustomerManagement\Application\UseCases\Queries\FindAllRejectedReasonsQuery;
use Src\Company\CustomerManagement\Application\UseCases\Queries\FindRejectedReasonQuery;

class RejectedReasonController extends Controller
{

    public function index(): JsonResponse
    {
        try {

            return response()->success((new FindAllRejectedReasonsQuery())->handle(), "RejectedReason List", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function show($id): JsonResponse
    {
        try {

            return response()->success((new FindRejectedReasonQuery($id))->handle(), "Rejected Reason Detail", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {

            $rejectedReason = RejectedReasonMapper::fromRequest($request);

            $rejectedReasonData = (new StoreRejectedReasonsCommand($rejectedReason))->execute();

            return response()->success($rejectedReasonData, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function update(int $id, Request $request): JsonResponse
    {
        try {
            $rejectedReason = RejectedReasonMapper::fromRequest($request, $id);

            $rejectedReasonData = (new UpdateRejectedReasonCommand($rejectedReason))->execute();

            return response()->success($rejectedReasonData, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->json($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function orderUpdate(Request $request)
    {
        try {
            $rejectedReason = $request->rejectedReasons;

            $rejectedReasonData = (new UpdateRejectedReasonOrderCommand($rejectedReason))->execute();

            return response()->success($rejectedReasonData, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->json($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            (new DeleteRejectedReasonCommand($id))->execute();

            return response()->success($id, "Deleted Successfully", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }
}
