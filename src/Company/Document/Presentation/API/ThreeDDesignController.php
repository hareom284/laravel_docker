<?php

namespace Src\Company\Document\Presentation\API;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Src\Company\Document\Application\Mappers\ThreeDDesignMapper;
use Symfony\Component\HttpFoundation\Response;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\Document\Domain\Repositories\ThreeDDesignRepositoryInterface;
use Src\Company\Document\Domain\Model\Entities\ThreeDDesign;
use Src\Company\Document\Infrastructure\EloquentModels\ThreeDDesignEloquentModel;
use Src\Company\Document\Application\UseCases\Queries\FindThreeDDesignByProjectIdQuery;
use Src\Company\Document\Application\UseCases\Queries\FindThreeDDesignByIdQuery;
use Src\Company\Document\Application\UseCases\Commands\StoreThreeDDesignCommand;
use Src\Company\Document\Application\UseCases\Commands\UpdateThreeDDesignCommand;
use Src\Company\Document\Application\UseCases\Commands\DeleteThreeDDesignCommand;
use Src\Company\Document\Application\Policies\ThreeDPolicy;

class ThreeDDesignController extends Controller
{

    public function getByProjectId(int $projectId)
    {
        abort_if(authorize('view', ThreeDPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for 3D Design!');
        try {

            return response()->success((new FindThreeDDesignByProjectIdQuery($projectId))->handle(), 'success', Response::HTTP_CREATED);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function show(int $id)
    {
        abort_if(authorize('view', ThreeDPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for 3D Design!');

        try {

            return response()->success((new FindThreeDDesignByIdQuery($id))->handle(), 'success', Response::HTTP_CREATED);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function store(Request $request)
    {
        abort_if(authorize('store', ThreeDPolicy::class), Response::HTTP_FORBIDDEN, 'Need store permission for 3D Design!');

        try {
            $threeDDesign = ThreeDDesignMapper::fromRequest($request);

            $designData = (new StoreThreeDDesignCommand($threeDDesign))->execute();

            return response()->success($designData, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function update(int $id, Request $request)
    {
        abort_if(authorize('update', ThreeDPolicy::class), Response::HTTP_FORBIDDEN, 'Need update permission for 3D Design!');

        try {
            $threeDDesign = ThreeDDesignMapper::fromRequest($request, $id);

            $designData = (new UpdateThreeDDesignCommand($threeDDesign))->execute();

            return response()->success($designData, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function delete(int $id)
    {
        abort_if(authorize('destroy', ThreeDPolicy::class), Response::HTTP_FORBIDDEN, 'Need destroy permission for 3D Design!');

        try {
            (new DeleteThreeDDesignCommand($id))->execute();

            return response()->success($id, "Successfully Deleted", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    // public function index(int $projectId): JsonResponse
    // {

    //     try {

    //         return response()->success((new FindAllDesignWorkQuery($projectId))->handle(),'success', Response::HTTP_CREATED);

    //     } catch (UnauthorizedUserException $e) {

    //         return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
    //     }
    // }

    // public function sign(Request $request): JsonResponse
    // {
    //     try {
    //         $fileName =  time().'.'.$request->file('signature')->extension();

    //         $filePath = 'design_work_file/sign/' . $fileName;

    //          DesignWorkEloquentModel::where('id', $request->design_work_id)->update([
    //                 'signature' => $filePath,
    //                 'last_edited' => Carbon::now(),
    //                 'signed_date' => $request->sign_date,
    //                 'drafter_in_charge_id' => $request->drafter_id ? $request->drafter_id : null
    //         ]);

    //         return response()->success(null, 'success', Response::HTTP_CREATED);

    //     }catch (\DomainException $e) {

    //         return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);

    //     } catch (UnauthorizedUserException $e) {

    //         return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
    //     }
    // }

    // public function show(int $id): JsonResponse
    // {

    //     try {
    //         return response()->success((new FindDesignWorkByIdQuery($id))->handle(),'success', Response::HTTP_CREATED);

    //     } catch (UnauthorizedUserException $e) {

    //         return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
    //     }
    // }

    // public function store(StoreDesignWorkRequest $request): JsonResponse
    // {

    //     try {
    //         $designWork = DesignWorkMapper::fromRequest($request);

    //         $designWorkData = (new StoreDesignWorkCommand($designWork, json_decode($request->salepersons_id), json_decode($request->materials)))->execute();

    //         return response()->success($designWorkData,'success', Response::HTTP_CREATED);

    //     } catch (\DomainException $e) {

    //         return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);

    //     } catch (UnauthorizedUserException $e) {

    //         return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
    //     }
    // }

    // public function update(int $id, Request $request): JsonResponse
    // {

    //     try {
    //         $designWork = DesignWorkMapper::fromRequest($request, $id);

    //         (new UpdateDesignWorkCommand($designWork, json_decode($request->salepersons_id), json_decode($request->materials)))->execute();

    //         return response()->success($designWork,'success',Response::HTTP_OK);

    //     } catch (\DomainException $e) {

    //         return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);

    //     } catch (UnauthorizedUserException $e) {

    //         return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
    //     }
    // }

    // public function destroy(int $id): JsonResponse
    // {

    //     try {
    //         (new DeleteDesignWorkCommand($id))->execute();

    //         return response()->success($id,"Successfully Deleted", Response::HTTP_OK);

    //     } catch (UnauthorizedUserException $e) {

    //         return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
    //     }
    // }

}
