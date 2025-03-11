<?php

namespace Src\Company\Document\Presentation\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Src\Company\Document\Application\Mappers\EvoTemplateItemMapper;
use Src\Company\Document\Application\Mappers\EvoTemplateRoomMapper;
use Src\Company\Document\Application\UseCases\Commands\StoreEvoTemplateItemCommand;
use Src\Company\Document\Application\UseCases\Commands\StoreEvoTemplateRoomCommand;
use Src\Company\Document\Application\UseCases\Commands\DeleteEvoTemplateItemCommand;
use Src\Company\Document\Application\UseCases\Commands\DeleteEvoTemplateRoomCommand;
use Src\Company\Document\Application\UseCases\Queries\FindAllEvoTemplateItemQuery;
use Src\Company\Document\Application\UseCases\Queries\FindAllEvoTemplateRoomQuery;
use Symfony\Component\HttpFoundation\Response;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\Document\Application\Requests\StoreEvoTemplateItemRequest;
use Src\Company\Document\Application\Requests\StoreEvoTemplateRoomRequest;
use Src\Company\Document\Application\Policies\EvoTemplatePolicy;
use Src\Company\Document\Application\Requests\UpdateEvoTemplateItemRequest;
use Src\Company\Document\Application\UseCases\Commands\UpdateEvoTemplateItemCommand;
use Src\Company\Document\Application\UseCases\Commands\UpdateEvoTemplateRoomCommand;
use Src\Company\Document\Application\UseCases\Queries\FindEvoTemplateQuery;

class EvoTemplateController extends Controller
{
    public function index(): JsonResponse
    {
        abort_if(authorize('view', EvoTemplatePolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for EVO Template!');

        try {
            $template = (new FindEvoTemplateQuery())->handle();

            return response()->success($template, 'success', Response::HTTP_OK);
        }catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function templateItemList(): JsonResponse
    {
        abort_if(authorize('view', EvoTemplatePolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for EVO Template!');
        // abort_if(authorize('view', EvoTemplatePolicy::class), Response::HTTP_FORBIDDEN, '403 Forbidden');

        try {

            $items = (new FindAllEvoTemplateItemQuery())->handle();

            return response()->success($items, 'success', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function templateRoomList(): JsonResponse
    {
        abort_if(authorize('view', EvoTemplatePolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for EVO Template!');
        // abort_if(authorize('view', EvoTemplatePolicy::class), Response::HTTP_FORBIDDEN, '403 Forbidden');

        try {

            $rooms = (new FindAllEvoTemplateRoomQuery())->handle();

            return response()->success($rooms, 'success', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function storeItem(StoreEvoTemplateItemRequest $request): JsonResponse
    {
        abort_if(authorize('store', EvoTemplatePolicy::class), Response::HTTP_FORBIDDEN, 'Need store permission for EVO Template!');

        try {

            $item = EvoTemplateItemMapper::fromRequest($request);

            $itemData = (new StoreEvoTemplateItemCommand($item))->execute();

            return response()->success($itemData, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function updateItem(UpdateEvoTemplateItemRequest $request,$itemId): JsonResponse
    {
        abort_if(authorize('update', EvoTemplatePolicy::class), Response::HTTP_FORBIDDEN, 'Need update permission for EVO Template!');

        try {

            $item = EvoTemplateItemMapper::fromRequest($request,$itemId);

            $itemData = (new UpdateEvoTemplateItemCommand($item))->execute();

            return response()->success($itemData, 'success', Response::HTTP_OK);
            
        } catch (\DomainException $e) {
            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {
            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function storeRoom(StoreEvoTemplateRoomRequest $request): JsonResponse
    {
        abort_if(authorize('store', EvoTemplatePolicy::class), Response::HTTP_FORBIDDEN, 'Need store permission for EVO Template!');

        try {

            $room = EvoTemplateRoomMapper::fromRequest($request);

            $roomData = (new StoreEvoTemplateRoomCommand($room))->execute();

            return response()->success($roomData, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function updateRoom(StoreEvoTemplateRoomRequest $request,$itemId): JsonResponse
    {
        abort_if(authorize('update', EvoTemplatePolicy::class), Response::HTTP_FORBIDDEN, 'Need update permission for EVO Template!');

        try {

            $item = EvoTemplateRoomMapper::fromRequest($request,$itemId);

            $itemData = (new UpdateEvoTemplateRoomCommand($item))->execute();

            return response()->success($itemData, 'success', Response::HTTP_OK);
            
        } catch (\DomainException $e) {
            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {
            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function destroyItem(int $id): JsonResponse
    {
        abort_if(authorize('destroy', EvoTemplatePolicy::class), Response::HTTP_FORBIDDEN, 'Need destroy permission for EVO Template!');

        try {
            (new DeleteEvoTemplateItemCommand($id))->execute();

            return response()->success($id, "Successfully Deleted", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function destroyRoom(int $id): JsonResponse
    {
        abort_if(authorize('destroy', EvoTemplatePolicy::class), Response::HTTP_FORBIDDEN, 'Need destroy permission for EVO Template!');

        try {
            (new DeleteEvoTemplateRoomCommand($id))->execute();

            return response()->success($id, "Successfully Deleted", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }
}
