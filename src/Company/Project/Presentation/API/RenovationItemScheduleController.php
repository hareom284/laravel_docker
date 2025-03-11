<?php

namespace Src\Company\Project\Presentation\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Src\Company\Project\Application\Mappers\RenovationItemScheduleMapper;
use Src\Company\Project\Application\UseCases\Commands\UpdateScheduleCommand;
use Src\Company\Project\Application\UseCases\Commands\UpdateScheduleStatusCommand;
use Src\Company\Project\Application\UseCases\Queries\FindEventByIdQuery;
use Symfony\Component\HttpFoundation\Response;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\Project\Application\UseCases\Queries\FindAllRenovationItemSchedulesQuery;
use Src\Company\Project\Application\UseCases\Queries\FindRenovationItemCountWithSectionQuery;
use Src\Company\Project\Application\UseCases\Queries\FindScheduleDateByProjectIdQuery;
use Src\Company\Project\Application\UseCases\Commands\UpdateAllEvoItemsStatusCommand;
use Src\Company\Project\Application\UseCases\Commands\UpdateAllScheduleStatusCommand;
use Src\Company\Project\Application\UseCases\Commands\UpdateEvoItemStatusCommand;
use Src\Company\Project\Application\UseCases\Commands\UpdateEvoScheduleCommand;
use Src\Company\Project\Application\UseCases\Commands\UpdateScheduleMobileCommand;
use Src\Company\Project\Application\UseCases\Commands\UpdateScheduleStatusMobileCommand;
use Src\Company\Project\Application\UseCases\Queries\FindAllEvoItemsScheduleQuery;

class RenovationItemScheduleController extends Controller
{
    public function index($projectId): JsonResponse
    {
        //check if user's has permission
        //abort_if(authorize('view', EventPolicy::class), Response::HTTP_FORBIDDEN, '403 Forbidden');

        try {

            $data['date'] = (new FindScheduleDateByProjectIdQuery($projectId))->handle(); // to get min start date and max end date

            $data['data'] = (new FindAllRenovationItemSchedulesQuery($projectId))->handle(); // to get data by document type and section

            $data['itemCount'] = (new FindRenovationItemCountWithSectionQuery($projectId))->handle(); // to get reno item count

            return response()->success($data,"success",Response::HTTP_OK);

        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function getEvo($projectId): JsonResponse
    {
        //check if user's has permission
        //abort_if(authorize('view', EventPolicy::class), Response::HTTP_FORBIDDEN, '403 Forbidden');

        try {

            $evoItems = (new FindAllEvoItemsScheduleQuery($projectId))->handle();

            return response()->success($evoItems,"success",Response::HTTP_OK);

        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function updateSchedule(Request $request): JsonResponse
    {
        //check if user's has permission
        //abort_if(authorize('update', EventPolicy::class), Response::HTTP_FORBIDDEN, '403 Forbidden');

        try {

            $scheduleRequests = $request->all();

            $scheduleData = (new UpdateScheduleMobileCommand($scheduleRequests))->execute();

            return response()->success($scheduleData,'Success', Response::HTTP_OK);

        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);

        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function updateStatus($id,Request $request): JsonResponse
    {
        try {

            $scheduleRequests = $request->all();

            $scheduleData = (new UpdateScheduleStatusMobileCommand($scheduleRequests,$id))->execute();

            return response()->success($scheduleData,'Success', Response::HTTP_OK);

        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);

        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function updateAllStatus(Request $request): JsonResponse
    {
        try {

            $itemsIds = $request->itemsIds;

            $isChecked = $request->isChecked;

            $scheduleData = (new UpdateAllScheduleStatusCommand($itemsIds,$isChecked))->execute();

            return response()->success($scheduleData,'Success', Response::HTTP_OK);

        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);

        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function updateEvoItemStatus($id,Request $request): JsonResponse
    {
        try {

            $status = $request->is_checked;

            $roomId = $request->roomId;

            $scheduleData = (new UpdateEvoItemStatusCommand($status,$id,$roomId))->execute();

            return response()->success($scheduleData,'Success', Response::HTTP_OK);

        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);

        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function updateEvoAllItemStatus(Request $request): JsonResponse
    {
        try {

            $evoId = $request->evoId;

            $isChecked = $request->isChecked;

            $scheduleData = (new UpdateAllEvoItemsStatusCommand($evoId,$isChecked))->execute();

            return response()->success($scheduleData,'Success', Response::HTTP_OK);

        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);

        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function updateEvoSchedule(Request $request): JsonResponse
    {
        //check if user's has permission
        //abort_if(authorize('update', EventPolicy::class), Response::HTTP_FORBIDDEN, '403 Forbidden');

        try {

            $scheduleRequests = $request->all();

            $scheduleData = (new UpdateEvoScheduleCommand($scheduleRequests))->execute();

            return response()->success($scheduleData,'Success', Response::HTTP_OK);

        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);

        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }
}