<?php

namespace Src\Company\Document\Presentation\API;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\Document\Application\Mappers\VendorCategoryMapper;
use Src\Company\Document\Application\UseCases\Commands\DeleteVendorCategoryCommand;
use Src\Company\Document\Application\UseCases\Commands\StoreVendorCategoryCommand;
use Src\Company\Document\Application\UseCases\Commands\UpdateVendorCategoryCommand;
use Src\Company\Document\Application\UseCases\Queries\FindAllVendorCategoryQuery;
use Src\Company\Document\Application\Policies\VendorCategoryPolicy;


class VendorCategoryController extends Controller
{

    public function index()
    {
        abort_if(authorize('view', VendorCategoryPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Vendor Category!');

        try {

            $vendorCategoryLists = (new FindAllVendorCategoryQuery())->handle();

            return response()->success($vendorCategoryLists, 'Vendor Categories Lists', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function store(Request $request)
    {
        abort_if(authorize('store', VendorCategoryPolicy::class), Response::HTTP_FORBIDDEN, 'Need store permission for Vendor Category!');

        try {

            $vendorCategory = VendorCategoryMapper::fromRequest($request);

            (new StoreVendorCategoryCommand($vendorCategory))->execute();

            return response()->success(null, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function update(Request $request, $vendorCategoryId)
    {
        abort_if(authorize('update', VendorCategoryPolicy::class), Response::HTTP_FORBIDDEN, 'Need update permission for Vendor Category!');

        try {

            $vendorCategory = VendorCategoryMapper::fromRequest($request, $vendorCategoryId);

            $vendorCategoryData = (new UpdateVendorCategoryCommand($vendorCategory))->execute();

            return response()->success($vendorCategoryData, 'success', Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {

            return response()->error($e->getMessage(), Response::HTTP_NOT_FOUND);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function destroy($vendorCategoryId)
    {
        abort_if(authorize('destroy', VendorCategoryPolicy::class), Response::HTTP_FORBIDDEN, 'Need destroy permission for Vendor Category!');

        try {

            (new DeleteVendorCategoryCommand($vendorCategoryId))->execute();

            return response()->success(null, 'success', Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {

            return response()->error($e->getMessage(), Response::HTTP_NOT_FOUND);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }
}
