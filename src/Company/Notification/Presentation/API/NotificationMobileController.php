<?php

namespace Src\Company\Notification\Presentation\API;


use Illuminate\Http\Request;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Symfony\Component\HttpFoundation\Response;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\Notification\Application\UseCases\Commands\DeleteAllUserNotificationsCommand;
use Src\Company\Notification\Application\UseCases\Commands\DeleteUserNotificationCommand;
use Src\Company\Notification\Application\UseCases\Commands\MakeReadNotificationMobileCommand;
use Src\Company\Notification\Application\UseCases\Commands\StoreUserNotificationCommand;
use Src\Company\Notification\Application\UseCases\Queries\GetAppNotificationsMobileQuery;
use Src\Company\Notification\Application\UseCases\Queries\GetAppNotificationStatusQuery;
use Src\Company\Notification\Application\UseCases\Queries\GetNotificationSettingQuery;
use Src\Company\Notification\Application\UseCases\Queries\GetUserNotificationsQuery;

class NotificationMobileController extends Controller
{
    public function getAppNoti()
    {
        try {

            $result = (new GetAppNotificationsMobileQuery())->handle();

            return response()->success($result, "Notification Lists", Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        } catch (\Exception $e) {
            return response()->error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function makeRead($id)
    {
        try {

            $result = (new MakeReadNotificationMobileCommand($id))->execute();

            return response()->success($result, "Notification Lists", Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        } catch (\Exception $e) {
            return response()->error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getNotiStatus()
    {
        try {

            $result = (new GetAppNotificationStatusQuery())->handle();

            return response()->success($result, "Notification Status", Response::HTTP_OK);
            
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        } catch (\Exception $e) {
            return response()->error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
