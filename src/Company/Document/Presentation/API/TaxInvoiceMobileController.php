<?php

namespace Src\Company\Document\Presentation\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Src\Common\Infrastructure\Laravel\Controller;
use Symfony\Component\HttpFoundation\Response;
use Src\Company\Document\Application\Policies\TaxInvoicePolicy;
use Src\Company\Document\Application\UseCases\Commands\SignTaxByManagerCommand;
use Src\Company\Document\Application\UseCases\Commands\SignTaxBySaleMobileCommand;
use Src\Company\Document\Application\UseCases\Queries\FindTaxByIdMobileQuery;
use Src\Company\Document\Application\UseCases\Queries\FindTaxInvoiceByProjectIdMobileQuery;
use Src\Company\Document\Application\UseCases\Queries\GetTaxInvoicesByStatusOrder;

class TaxInvoiceMobileController extends Controller
{
    public function index($id): JsonResponse
    {
        abort_if(authorize('view', TaxInvoicePolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Statement of Account!');

        try {
            $taxInvoices = (new FindTaxInvoiceByProjectIdMobileQuery($id))->handle();
            return response()->success($taxInvoices, "Success", Response::HTTP_OK);

        } catch (\Exception $ex) {

            return response()->json(['error' => $ex->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id): JsonResponse
    {
        abort_if(authorize('view', TaxInvoicePolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Statement of Account!');

        try {

            $final_result = (new FindTaxByIdMobileQuery($id))->handle();

            return response()->success($final_result, "Success", Response::HTTP_OK);
        } catch (\Exception $ex) {

            return response()->json(['error' => $ex->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function TaxInvoiceSignBySaleperson(Request $request): JsonResponse
    {
        abort_if(authorize('sign_by_salesperson', TaxInvoicePolicy::class), Response::HTTP_FORBIDDEN, 'Need sign_by_salesperson permission for Statement of Account!');

        try {
            (new SignTaxBySaleMobileCommand($request))->execute();

            return response()->success('', "Statement Of Account Create Successful !", Response::HTTP_OK);

        } catch (\Exception $ex) {

            return response()->json(['error' => $ex->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getByStatusOrder(Request $request): JsonResponse
    {
        abort_if(authorize('view', TaxInvoicePolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Statement of Account!');

        try {

            $filters = $request->all();

            $taxInvoices = (new GetTaxInvoicesByStatusOrder($filters))->handle();

            return response()->success($taxInvoices, 'Statement of Account lists ordered by pending,approved', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function TaxInvoiceSignByManager(Request $request): JsonResponse
    {
        // abort_if(authorize('sign_by_manager', TaxInvoicePolicy::class), Response::HTTP_FORBIDDEN, 'Need sign_by_manager permission for Statement of Account!');

        try {
            (new SignTaxByManagerCommand($request))->execute();

            return response()->success('', 'Statement of Account Approve Successful !', Response::HTTP_CREATED);
        } catch (\DomainException $domainException) {

            return response()->json(['error' => $domainException->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

}
