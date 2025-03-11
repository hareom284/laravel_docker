<?php

namespace Src\Company\System\Presentation\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Src\Company\System\Application\Mappers\GeneralSettingMapper;
use Src\Company\System\Application\UseCases\Commands\UpdateGeneralSettingCommand;
use Symfony\Component\HttpFoundation\Response;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\System\Application\Requests\UpdateGeneralSettingRequest;
use Src\Company\System\Application\UseCases\Commands\UpdateAccountingSettingCommand;
use Src\Company\System\Application\UseCases\Queries\FindAccountingSettingByNameQuery;

class AccountingSettingController extends Controller
{

    public function show($companyId)
    {
        try {

            $data = (new FindAccountingSettingByNameQuery($companyId))->handle();

            return response()->success($data, "General Setting", Response::HTTP_OK);

        } catch (UnauthorizedUserException $e) {

            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }
    
    public function update(Request $request): JsonResponse
    {
        try {

            $settings = $request->body;

            (new UpdateAccountingSettingCommand($settings))->execute();

            return response()->success(null, 'success', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }
}
