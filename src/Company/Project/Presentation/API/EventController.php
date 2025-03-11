<?php

namespace Src\Company\Project\Presentation\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Src\Company\Project\Application\Mappers\EventMapper;
use Src\Company\Project\Application\UseCases\Commands\DeleteEventCommand;
use Src\Company\Project\Application\UseCases\Commands\StoreEventCommand;
use Src\Company\Project\Application\UseCases\Commands\UpdateEventCommand;
use Src\Company\Project\Application\UseCases\Queries\FindEventByIdQuery;
use Symfony\Component\HttpFoundation\Response;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\Project\Application\UseCases\Queries\FindEventCommentByEventIdQuery;
use Src\Company\Project\Domain\Repositories\EventRepositoryInterface;
use Src\Company\Project\Application\Policies\EventPolicy;

class EventController extends Controller
{
    private $eventInterFace;

    public function __construct(EventRepositoryInterface $eventRepository)
    {
        $this->eventInterFace = $eventRepository;
    }

    public function index(Request $request): JsonResponse
    {
        //check if user's has permission
        abort_if(authorize('view', EventPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Event!');

        try {

            $filters = $request;

            $events = $this->eventInterFace->getEvents();

            return response()->success($events, "success", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function eventsByGroup()
    {
        abort_if(authorize('view', EventPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Event!');

        try {

            $events = $this->eventInterFace->getEventsByGroup();

            return response()->success($events, "success", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function eventsByProjectId($projectId)
    {
        //check if user's has permission
        abort_if(authorize('view', EventPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Event!');

        try {

            $events = $this->eventInterFace->getEventsByProjectId($projectId);

            return response()->success($events, "success", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function show(int $id): JsonResponse
    {
        //check if user's has permission
        abort_if(authorize('view', EventPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Event!');

        try {
            return response()->json((new FindEventByIdQuery($id))->handle());
        } catch (UnauthorizedUserException $e) {

            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function eventCommentByEventId($event_id)
    {
        //check if user's has permission
        abort_if(authorize('view', EventPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Event!');

        try {
            return response()->success((new FindEventCommentByEventIdQuery($event_id))->handle(), 'Success', Response::HTTP_CREATED);
        } catch (UnauthorizedUserException $e) {

            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function store(Request $request): JsonResponse
    {
        //check if user's has permission
        abort_if(authorize('store', EventPolicy::class), Response::HTTP_FORBIDDEN, 'Need store permission for Event!');

        try {
            $event = EventMapper::fromRequest($request);

            $eventComments = $request->comment;

            $eventData = (new StoreEventCommand($event, $eventComments))->execute();

            return response()->success($eventData, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function update(int $id, Request $request): JsonResponse
    {
        //check if user's has permission
        abort_if(authorize('update', EventPolicy::class), Response::HTTP_FORBIDDEN, 'Need update permission for Event!');

        try {

            $event = EventMapper::fromRequest($request, $id);

            $eventComments = $request->comment;

            $eventData = (new UpdateEventCommand($event, $eventComments))->execute();

            return response()->success($eventData, 'Success', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function changeStatus(int $id, Request $request): JsonResponse
    {
        //check if user's has permission
        abort_if(authorize('update', EventPolicy::class), Response::HTTP_FORBIDDEN, 'Need update permission for Event!');

        try {

            $status = $request->status;

            $event = $this->eventInterFace->changeStatus($id, $status);

            return response()->success($event, Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        //check if user's has permission
        abort_if(authorize('destroy', EventPolicy::class), Response::HTTP_FORBIDDEN, 'Need destroy permission for Event!');

        try {
            (new DeleteEventCommand($id))->execute();

            return response()->success($id, "Sucessfully deleted", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }
}
