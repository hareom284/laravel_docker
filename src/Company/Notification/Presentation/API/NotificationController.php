<?php

namespace Src\Company\Notification\Presentation\API;


use Illuminate\Http\Request;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Symfony\Component\HttpFoundation\Response;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\Notification\Application\UseCases\Commands\DeleteAllUserNotificationsCommand;
use Src\Company\Notification\Application\UseCases\Commands\DeleteUserNotificationCommand;
use Src\Company\Notification\Application\UseCases\Commands\StoreUserNotificationCommand;
use Src\Company\Notification\Application\UseCases\Queries\GetNotificationSettingQuery;
use Src\Company\Notification\Application\UseCases\Queries\GetUserNotificationsQuery;

class NotificationController extends Controller
{
    public function store(Request $request)
    {
        try {
            $message_type = $request->message_type;
            $user_ids = $request->user_ids;
            $title = $request->title;
            $message = $request->message;

            $noti_type = (new GetNotificationSettingQuery($message_type))->handle();

            $result = (new StoreUserNotificationCommand($title, $message, $user_ids, $noti_type))->execute();

            return response()->success($result, "Successfully Send Notification.", Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        } catch (\Exception $e) {
            logger(['error',$e]);
            return response()->error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function index(Request $request)
    {
        try {
            $filterType = $request->input('filter', 'all'); // 'all', 'sent', 'received'
            $perPage = $request->input('per_page', null);
            $result = (new GetUserNotificationsQuery($filterType, $perPage))->handle();

            return response()->success($result, "Notification Lists", Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        } catch (\Exception $e) {
            return response()->error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $action = $request->action;
            $id = $request->id;
            if (isset($action) && $action == 'all' && !$id) {
                $result = (new DeleteAllUserNotificationsCommand())->execute();
                return response()->success($result, 'Successfully Deleted!', Response::HTTP_OK);
                return $result;
            } else {
                $result = (new DeleteUserNotificationCommand($id))->execute();
                return response()->success($result, 'Successfully Deleted!', Response::HTTP_OK);
            }
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        } catch (\Exception $e) {
            return response()->error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
