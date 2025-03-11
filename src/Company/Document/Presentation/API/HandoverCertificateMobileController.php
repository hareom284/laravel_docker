<?php

namespace Src\Company\Document\Presentation\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\Document\Application\Mappers\HandoverCertificateMapper;
use Src\Company\Document\Application\Policies\HandoverCertificatePolicy;
use Src\Company\Document\Application\UseCases\Commands\CreateHandoverCertificateMobileCommand;
use Src\Company\Document\Application\UseCases\Commands\SignCustomerHandoverCertificateMobileCommand;
use Src\Company\Document\Application\UseCases\Queries\FindHandoverByProjectIdMobileQuery;
use Src\Company\Document\Application\UseCases\Queries\FindHandoverByProjectIdQuery;
use Src\Company\Document\Application\UseCases\Queries\FindHandoverCertificateByIdMobileQuery;

class HandoverCertificateMobileController extends Controller
{

    public function index($id)
    {
        abort_if(authorize('view', HandoverCertificatePolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Handover Certificate!');

        try {

            $handOverList = ((new FindHandoverByProjectIdMobileQuery($id))->handle());

            return response()->success($handOverList, 'success', Response::HTTP_OK);
        } catch (\Exception $ex) {

            return response()->error($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function create(Request $request)
    {
        // abort_if(authorize('store', HandoverCertificatePolicy::class), Response::HTTP_FORBIDDEN, 'Need store permission for Handover Certificate!');

        try {

            // $handoverData = HandoverCertificateMapper::fromRequest($request);

            $handoverData = (new CreateHandoverCertificateMobileCommand($request))->execute();

            return response()->success('', 'Handover Certificate Create Successful !', Response::HTTP_OK);

        } catch (\Exception $ex) {

            return response()->error($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function HandoverCertificateDetail($id)
    {
        // abort_if(authorize('view', HandoverCertificatePolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Handover Certificate!');

        try {

            $handoverDetail = ((new FindHandoverCertificateByIdMobileQuery($id))->handle());

            return response()->success($handoverDetail, 'success', Response::HTTP_OK);
        } catch (\Exception $ex) {

            return response()->error($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function customerSign(Request $request)
    {
        // abort_if(authorize('sign_by_customer', HandoverCertificatePolicy::class), Response::HTTP_FORBIDDEN, 'Need sign_by_customer permission for Handover Certificate!');

        try {

            (new SignCustomerHandoverCertificateMobileCommand($request))->execute();

            return response()->success($request, 'Handover Certificate Sign Successful !', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }


}
