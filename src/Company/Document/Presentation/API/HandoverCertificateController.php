<?php

namespace Src\Company\Document\Presentation\API;

use Illuminate\Http\Client\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Http\Response;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\Document\Application\Mappers\HandoverCertificateMapper;
use Src\Company\Document\Application\UseCases\Commands\CreateHandoverCertificateCommand;
use Src\Company\Document\Application\UseCases\Commands\ManagerSignPurchaseOrderCommand;
use Src\Company\Document\Application\UseCases\Commands\SignCustomerHandoverCertificateCommand;
use Src\Company\Document\Application\UseCases\Commands\SignManagerHandoverCertificateCommand;
use Src\Company\Document\Application\UseCases\Queries\FindAllHandoverCertificateQuery;
use Src\Company\Document\Application\UseCases\Queries\FindApproveHandoverCertificateListsQuery;
use Src\Company\Document\Application\UseCases\Queries\FindHandoverByProjectIdQuery;
use Src\Company\Document\Application\UseCases\Queries\FindHandoverCertificateByIdQuery;
use Src\Company\Document\Application\Policies\HandoverCertificatePolicy;
use Src\Company\Document\Application\UseCases\Commands\SignHandoverCertificateCommand;

class HandoverCertificateController extends Controller
{

    public function HandoverCertificateLists()
    {
        abort_if(authorize('view', HandoverCertificatePolicy::class), Response::HTTP_FORBIDDEN, 'Need store permission for Handover Certificate!');

        try {

            $handoverLists = ((new FindAllHandoverCertificateQuery())->handle());

            return response()->success($handoverLists, 'success', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function HandoverCertificateListsByProjectId($projectId)
    {
        abort_if(authorize('view_by_project', HandoverCertificatePolicy::class), Response::HTTP_FORBIDDEN, 'Need view_by_project permission for Handover Certificate!');

        try {

            $handoverLists = ((new FindHandoverByProjectIdQuery($projectId))->handle());

            return response()->success($handoverLists, 'success', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function approveRequireHandoverCertificates()
    {
        abort_if(authorize('view', HandoverCertificatePolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Handover Certificate!');

        try {

            $handoverLists = ((new FindApproveHandoverCertificateListsQuery())->handle());

            return response()->success($handoverLists, 'success', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function HandoverCertificateDetail($id)
    {
        abort_if(authorize('view', HandoverCertificatePolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Handover Certificate!');

        try {

            $handoverDetail = ((new FindHandoverCertificateByIdQuery($id))->handle());

            return response()->success($handoverDetail, 'success', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function create(HttpRequest $request)
    {
        // abort_if(authorize('store', HandoverCertificatePolicy::class), Response::HTTP_FORBIDDEN, 'Need store permission for Handover Certificate!');

        try {

            // $handoverData = HandoverCertificateMapper::fromRequest($request);

            $handoverData = (new CreateHandoverCertificateCommand($request))->execute();

            return response()->success($handoverData, 'success', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function managerSign(HttpRequest $request)
    {
        abort_if(authorize('sign_by_manager', HandoverCertificatePolicy::class), Response::HTTP_FORBIDDEN, 'Need sign_by_manager permission for Handover Certificate!');

        try {

            (new SignManagerHandoverCertificateCommand($request))->execute();

            return response()->success(null, 'success', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function customerSign(HttpRequest $request)
    {
        abort_if(authorize('sign_by_customer', HandoverCertificatePolicy::class), Response::HTTP_FORBIDDEN, 'Need sign_by_customer permission for Handover Certificate!');

        try {

            (new SignCustomerHandoverCertificateCommand($request))->execute();

            return response()->success($request, 'success', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function handoverdownloadPdf (HttpRequest $request)
    {
        $company_folder_name = config('folder.company_folder_name');

        // return $request->handoverCustomers;
        $data = [
            'referenceNo' => $request->referenceNo,
            'clientArr' => $request->clientName,
            'street_name' => $request->street_name,
            'unit_num' => $request->unit_num,
            'block_num' => $request->block_num,
            'handoverDate' => $request->handoverDate,
            'companyName' => $request->companyName,
            'salepersonSignature' => $request->salepersonSignature,
            'salepersonName' => $request->salepersonName,
            'salepersonRank' => $request->salepersonRank,
            'handoverCustomers' => $request->handoverCustomers,
            'company_folder_name' => $company_folder_name
        ];

        $pdf = \PDF::loadView('pdf.Handover.handover', $data);
        $pdf->setOption('enable-javascript', true);
        $pdf->setOption('margin-top', 40);
        return $pdf->download("Handover Certificate.pdf");
    }

    public function handoverSign(HttpRequest $request)
    {
        try {

            (new SignHandoverCertificateCommand($request))->execute();

            return response()->success($request, 'success', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }
}
