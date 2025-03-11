<?php

namespace Src\Company\System\Presentation\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Src\Company\System\Application\Mappers\SiteSettingMapper;
use Src\Company\System\Application\UseCases\Commands\UpdateSiteSettingCommand;
use Symfony\Component\HttpFoundation\Response;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\System\Application\Policies\SiteSettingPolicy;
use Src\Company\System\Application\UseCases\Queries\FindSettingByIdQuery;
use Src\Company\System\Application\UseCases\Queries\GetSiteLogoAndFaviconQuery;
use Src\Company\System\Application\Requests\UpdateSiteSettingRequest;


class SiteSettingController extends Controller
{
    public function show($id)
    {
        abort_if(authorize('view', SiteSettingPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Site Setting!');

        try {
            return response()->success((new FindSettingByIdQuery($id))->handle(), "Site Setting", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function getLogoAndFavicon()
    {
        try {
            return response()->success((new GetSiteLogoAndFaviconQuery())->handle(), "Site Setting", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function update(int $id, UpdateSiteSettingRequest $request): JsonResponse
    {
        //check if user's has permission
        abort_if(authorize('update', SiteSettingPolicy::class), Response::HTTP_FORBIDDEN, 'Need update permission for Site Setting!');

        try {
            $site = SiteSettingMapper::fromRequest($request, $id);

            (new UpdateSiteSettingCommand($site))->execute();

            return response()->success($site, 'success', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }
}
