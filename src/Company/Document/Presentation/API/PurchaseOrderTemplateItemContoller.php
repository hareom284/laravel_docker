<?php

namespace Src\Company\Document\Presentation\API;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\Document\Application\Mappers\PurchaseOrderTemplateItemMapper;
use Src\Company\Document\Application\UseCases\Commands\DeletePurchaseOrderTemplateItem;
use Src\Company\Document\Application\UseCases\Commands\StorePurchaseOrderTemplateItemCommand;
use Src\Company\Document\Application\UseCases\Commands\UpdatePurchaseOrderTemplateItemCommand;
use Src\Company\Document\Application\UseCases\Queries\FindAllPurchaseOrderTemplateItemQuery;
use Src\Company\Document\Application\UseCases\Queries\GetPurchaseOrderTemplateItemsForPOCreateQuery;
use Src\Company\Document\Application\Policies\PurchaseOrderTemplateItemPolicy;

class PurchaseOrderTemplateItemContoller extends Controller
{
    public function index(Request $request)
    {
        abort_if(authorize('view', PurchaseOrderTemplateItemPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Purchase Order Template Item!');

        try {

            $filters = $request->all();

            $purchaseOrders = (new FindAllPurchaseOrderTemplateItemQuery($filters))->handle();

            return response()->success($purchaseOrders, 'success', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function getItmesForPoCreate($companyId, $vendorCategoryId)
    {
        abort_if(authorize('view', PurchaseOrderTemplateItemPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Purchase Order Template Item!');

        try {

            $purchaseOrders = (new GetPurchaseOrderTemplateItemsForPOCreateQuery($companyId, $vendorCategoryId))->handle();

            return response()->success($purchaseOrders, 'success', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function store(Request $request)
    {
        abort_if(authorize('store', PurchaseOrderTemplateItemPolicy::class), Response::HTTP_FORBIDDEN, 'Need store permission for Purchase Order Template Item!');

        try {

            $poTemplateItem = PurchaseOrderTemplateItemMapper::fromRequest($request);

            (new StorePurchaseOrderTemplateItemCommand($poTemplateItem))->execute();

            return response()->success(null, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function update(int $id, Request $request): JsonResponse
    {
        abort_if(authorize('update', PurchaseOrderTemplateItemPolicy::class), Response::HTTP_FORBIDDEN, 'Need update permission for Purchase Order Template Item!');

        try {

            $poTemplateItem = PurchaseOrderTemplateItemMapper::fromRequest($request, $id);

            $vendorCategoryData = (new UpdatePurchaseOrderTemplateItemCommand($poTemplateItem))->execute();

            return response()->success($vendorCategoryData, 'success', Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {

            return response()->error($e->getMessage(), Response::HTTP_NOT_FOUND);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        abort_if(authorize('destroy', PurchaseOrderTemplateItemPolicy::class), Response::HTTP_FORBIDDEN, 'Need destroy permission for Purchase Order Template Item!');

        try {
            (new DeletePurchaseOrderTemplateItem($id))->execute();

            return response()->success($id, "Successfully Deleted", Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {

            return response()->error($e->getMessage(), Response::HTTP_NOT_FOUND);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }
}
