<?php

namespace Src\Company\Document\Presentation\API;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\Document\Application\Mappers\DeliveryOrderMapper;
use Src\Company\Document\Application\UseCases\Commands\StoreDeliveryOrderCommand;
use Src\Company\Document\Application\UseCases\Queries\FindDeliveryOrderByIdQuery;
use Src\Company\Document\Application\UseCases\Queries\FindDeliveryOrderByProjectId;

class DeliveryOrderController extends Controller
{

    public function index(int $projectId): JsonResponse
    {
        try {

            return response()->success((new FindDeliveryOrderByProjectId($projectId))->handle(), 'success', Response::HTTP_CREATED);
        } catch (Exception $e) {

            return response()->error($e->getMessage(), Response::HTTP_EXPECTATION_FAILED);
        }
    }


    public function show(int $id): JsonResponse
    {
        try {
            return response()->success((new FindDeliveryOrderByIdQuery($id))->handle(), 'success', Response::HTTP_CREATED);
        } catch (Exception $e) {

            return response()->json(['error' => $e->getMessage()], Response::HTTP_EXPECTATION_FAILED);
        }
    }

    public function store(Request $request): JsonResponse
    {

        try {
            $deliveryOrder = DeliveryOrderMapper::fromRequest($request);

            $deliveryOrderData = (new StoreDeliveryOrderCommand($deliveryOrder))->execute();

            return response()->success($deliveryOrderData, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Exception $e) {

            return response()->error($e->getMessage(), Response::HTTP_EXPECTATION_FAILED);
        }
    }

}
