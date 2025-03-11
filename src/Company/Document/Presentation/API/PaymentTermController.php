<?php

namespace Src\Company\Document\Presentation\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Symfony\Component\HttpFoundation\Response;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\Document\Application\Mappers\PaymentTermMapper;
use Src\Company\Document\Application\UseCases\Commands\ApprovePaymentRequestCommand;
use Src\Company\Document\Application\UseCases\Commands\DeletePaymentTermCommand;
use Src\Company\Document\Application\UseCases\Commands\SendPaymentRequestCommand;
use Src\Company\Document\Application\UseCases\Commands\StorePaymentTermCommand;
use Src\Company\Document\Application\UseCases\Commands\UpdatePaymentTermCommand;
use Src\Company\Document\Application\UseCases\Queries\FindAllPaymentTermsQuery;
use Src\Company\Document\Application\UseCases\Queries\FindPaymentTermsByIdQuery;

class PaymentTermController extends Controller
{

    public function index(Request $request): JsonResponse
    {

        try {
            $filters = $request->all();
            return response()->success((new FindAllPaymentTermsQuery($filters))->handle(), "Payment Term List", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function store(Request $request): JsonResponse
    {

        try {

            $paymentTerm = PaymentTermMapper::fromRequest($request);

            $paymentTermData = (new StorePaymentTermCommand($paymentTerm))->execute();

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

            $paymentTerm = PaymentTermMapper::fromRequest($request, $request->id);

            (new UpdatePaymentTermCommand($paymentTerm))->execute();

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

            return response()->success((new FindPaymentTermsByIdQuery($id))->handle(), "Payment Term List", Response::HTTP_OK);

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

            (new DeletePaymentTermCommand($id))->execute();

            return response()->success($id, "Deleted Successfully", Response::HTTP_OK);

        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function sendRequest($id, Request $request)
    {
        try {

            return response()->success((new SendPaymentRequestCommand($id, $request))->execute(), "Payment Term Updated", Response::HTTP_OK);

        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function approveRequest($id)
    {
        try {

            return response()->success((new ApprovePaymentRequestCommand($id))->execute(), "Payment Term Approved", Response::HTTP_OK);

        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }
}
