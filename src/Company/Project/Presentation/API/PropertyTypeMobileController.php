<?php

namespace Src\Company\Project\Presentation\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Symfony\Component\HttpFoundation\Response;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\Project\Application\UseCases\Queries\FindAllPropertiesMobileQuery;

class PropertyTypeMobileController extends Controller
{

    public function index(): JsonResponse
    {
        try {
            $propertyTypeLists = (new FindAllPropertiesMobileQuery())->handle();

            return response()->success($propertyTypeLists,'success',Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

}