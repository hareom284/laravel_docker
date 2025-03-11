<?php

namespace Src\Company\CustomerManagement\Presentation\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Symfony\Component\HttpFoundation\Response;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\CustomerManagement\Application\Mappers\ReferrerFormMapper;
use Src\Company\CustomerManagement\Application\UseCases\Commands\DownloadReferrerFormCommand;
use Src\Company\CustomerManagement\Application\UseCases\Commands\SignReferrerFormCommand;
use Src\Company\CustomerManagement\Application\UseCases\Commands\StoreReferrerFormCommand;
use Src\Company\CustomerManagement\Application\UseCases\Queries\FindAllReferrerFormsQuery;
use Src\Company\CustomerManagement\Application\UseCases\Queries\FindApprovedReferrers;
use Src\Company\CustomerManagement\Application\UseCases\Queries\FindReferrerFormQuery;
use Src\Company\UserManagement\Application\Mappers\UserMapper;
use Src\Company\UserManagement\Application\UseCases\Commands\StoreUserCommand;

class ReferrerFormController extends Controller
{

    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->all(); 
            return response()->success((new FindAllReferrerFormsQuery($filters))->handle(), "Referre rForm List", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function show($id): JsonResponse
    {
        try {

            return response()->success((new FindReferrerFormQuery($id))->handle(), "Referrer Form Detail", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {

            $user = UserMapper::fromRequest($request);
            $userData = (new StoreUserCommand($user, null, [], null, null, null))->execute();
            $request['referrer_id'] = $userData->id;
            $referrerForm = ReferrerFormMapper::fromRequest($request);
            $referrerFormData = (new StoreReferrerFormCommand($referrerForm))->execute();

            return response()->success($referrerFormData, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function sign(int $id, Request $request): JsonResponse
    {
        try {
            $referrerForm = ReferrerFormMapper::fromRequest($request, $id);

            $referrerFormData = (new SignReferrerFormCommand($referrerForm))->execute();

            return response()->success($referrerFormData, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->json($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }


    public function downloadPdf(int $id): JsonResponse
    {
        try {
            (new DownloadReferrerFormCommand($id))->execute();

            return response()->success($id, "Download Successfully", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function getApprovedReferrer()
    {
        try {
            return response()->success((new FindApprovedReferrers())->handle(), "Referres List", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }
}
