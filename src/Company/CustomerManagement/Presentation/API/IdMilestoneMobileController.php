<?php

namespace Src\Company\CustomerManagement\Presentation\API;

use Illuminate\Http\JsonResponse;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Symfony\Component\HttpFoundation\Response;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\CustomerManagement\Application\UseCases\Queries\FindAllIdMilestonesMobileQuery;

class IdMilestoneMobileController extends Controller
{

    public function index(): JsonResponse
    {
        try {

            return response()->success((new FindAllIdMilestonesMobileQuery())->handle(), "Id Milestone List", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }
}
