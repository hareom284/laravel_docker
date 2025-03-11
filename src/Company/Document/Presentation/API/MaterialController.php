<?php

namespace Src\Company\Document\Presentation\API;

use Illuminate\Http\JsonResponse;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\Document\Application\UseCases\Queries\FindAllMaterialQuery;
use Symfony\Component\HttpFoundation\Response;

class MaterialController extends Controller
{

    public function index()
    {
        try {
            
            $materialLists = (new FindAllMaterialQuery())->handle();

            return response()->success($materialLists,'success', Response::HTTP_CREATED);

        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);

        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

}
