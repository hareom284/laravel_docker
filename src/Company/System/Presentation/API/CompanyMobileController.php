<?php

namespace Src\Company\System\Presentation\API;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Symfony\Component\HttpFoundation\Response;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\System\Application\Policies\CompanyPolicy;
use Src\Company\System\Application\UseCases\Queries\GetAllCompanyListMobileQuery;

class CompanyMobileController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        // //check if user's has permission
        // abort_if(authorize('view', CompanyPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Company!');

        try {

            $filters = $request->all();

            $companies = (new GetAllCompanyListMobileQuery($filters))->handle();

            return response()->success($companies, 'success', Response::HTTP_OK);
        } catch (Exception $ex) {

            return response()->error($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
