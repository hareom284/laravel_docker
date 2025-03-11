<?php

namespace Src\Company\Project\Presentation\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Symfony\Component\HttpFoundation\Response;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\Project\Application\Mappers\PaymentTypeMapper;
use Src\Company\Project\Application\UseCases\Commands\DeletePaymentTypeCommand;
use Src\Company\Project\Application\UseCases\Commands\StorePaymentTypeCommand;
use Src\Company\Project\Application\UseCases\Commands\UpdatePaymentTypeCommand;
use Src\Company\Project\Application\UseCases\Queries\FindAllPaymentTypesQuery;
use Src\Company\Project\Application\UseCases\Queries\FindPaymentTypesByIdQuery;

class PaymentTypeController extends Controller
{

    public function index(Request $request): JsonResponse
    {

        try {

            return response()->success((new FindAllPaymentTypesQuery())->handle(), "Payment Term List", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function store(Request $request): JsonResponse
    {

        try {

            $paymentTerm = PaymentTypeMapper::fromRequest($request);

            $paymentTermData = (new StorePaymentTypeCommand($paymentTerm))->execute();

            return response()->success($paymentTermData, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function update(Request $request): JsonResponse
    {

        try {

            $paymentTerm = PaymentTypeMapper::fromRequest($request, $request->id);

            (new UpdatePaymentTypeCommand($paymentTerm))->execute();

            return response()->success(null, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function show(int $id)
    {

        try {

            return response()->success((new FindPaymentTypesByIdQuery($id))->handle(), "Payment Term List", Response::HTTP_OK);

        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        //check if user's has permission

        try {

            (new DeletePaymentTypeCommand($id))->execute();

            return response()->success($id, "Deleted Successfully", Response::HTTP_OK);

        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }
}
