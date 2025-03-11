<?php

namespace Src\Company\System\Presentation\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Src\Company\System\Application\Mappers\GeneralSettingMapper;
use Src\Company\System\Application\UseCases\Commands\UpdateGeneralSettingCommand;
use Symfony\Component\HttpFoundation\Response;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\System\Application\Policies\GeneralSettingPolicy;
use Src\Company\System\Application\UseCases\Queries\FindGeneralSettingByNameQuery;
use Src\Company\System\Application\UseCases\Queries\FindAllGeneralSettingsQuery;
use Illuminate\Support\Facades\Log;

use Src\Company\System\Application\Requests\UpdateGeneralSettingRequest;


class GeneralSettingController extends Controller
{

    public function show($generalSetting)
    {
        try {
            return response()->success((new FindGeneralSettingByNameQuery($generalSetting))->handle(), "General Setting", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }
    public function showAll()
    {
        try {
            return response()->success((new FindAllGeneralSettingsQuery())->handle(), "General Setting", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function update(UpdateGeneralSettingRequest $request): JsonResponse
    {
        //check if user's has permission
        abort_if(authorize('update', GeneralSettingPolicy::class), Response::HTTP_FORBIDDEN, 'Need update permission for General Setting!');

        try {
            $general = GeneralSettingMapper::fromRequest($request);

            (new UpdateGeneralSettingCommand($general))->execute();

            return response()->success($general, 'success', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

}
