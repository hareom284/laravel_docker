<?php

namespace Src\Company\Document\Presentation\API;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Src\Company\Document\Application\Mappers\DesignWorkMapper;
use Src\Company\Document\Application\UseCases\Commands\StoreDesignWorkCommand;
use Src\Company\Document\Application\UseCases\Commands\DeleteDesignWorkCommand;
use Src\Company\Document\Application\UseCases\Commands\UpdateDesignWorkCommand;
use Src\Company\Document\Application\UseCases\Queries\FindDesignWorkByIdQuery;
use Src\Company\Document\Application\UseCases\Queries\FindAllDesignWorkQuery;
use Symfony\Component\HttpFoundation\Response;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\Document\Application\Requests\StoreDesignWorkRequest;
use Src\Company\Document\Domain\Repositories\DesignWorkRepositoryInterface;
use Src\Company\Document\Application\Policies\DesignWorkPolicy;
use Src\Company\Document\Domain\Model\Entities\DesignWork;
use Src\Company\Document\Infrastructure\EloquentModels\DesignWorkEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\ThreeDDesignEloquentModel;

class DesignWorkController extends Controller
{

    public function index(int $projectId): JsonResponse
    {
        //check if user's has permission
        abort_if(authorize('view', DesignWorkPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Design Work!');

        try {

            return response()->success((new FindAllDesignWorkQuery($projectId))->handle(), 'success', Response::HTTP_CREATED);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function sign(Request $request): JsonResponse
    {
        abort_if(authorize('sign', DesignWorkPolicy::class), Response::HTTP_FORBIDDEN, 'Need sign_project_design_work_document permission for Design Work!');

        try {
            $fileName =  time() . '.' . $request->file('signature')->extension();

            $filePath = 'design_work_file/sign/' . $fileName;

            DesignWorkEloquentModel::where('id', $request->design_work_id)->update([
                'signature' => $filePath,
                'last_edited' => Carbon::now(),
                'signed_date' => $request->sign_date,
                'drafter_in_charge_id' => $request->drafter_id ? $request->drafter_id : null
            ]);

            // if($request->drafter_id)
            // {
            //     $design_work = DesignWorkEloquentModel::find($request->design_work_id);

            //     ThreeDDesignEloquentModel::create([
            //         'project_id' => $design_work->project_id,
            //         'design_work_id' => $request->design_work_id,
            //         'drafter_id' => $request->drafter_id,
            //         'date' => null,
            //         'document_file' => null,
            //         'last_edited' => Carbon::now()
            //     ]);
            // }

            return response()->success(null, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function show(int $id): JsonResponse
    {
        //check if user's has permission
        abort_if(authorize('view', DesignWorkPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Design Work!');

        try {
            return response()->success((new FindDesignWorkByIdQuery($id))->handle(), 'success', Response::HTTP_CREATED);
        } catch (UnauthorizedUserException $e) {

            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function store(StoreDesignWorkRequest $request): JsonResponse
    {
        //check if user's has permission
        abort_if(authorize('store', DesignWorkPolicy::class), Response::HTTP_FORBIDDEN, 'Need store permission for Design Work!');

        try {
            $designWork = DesignWorkMapper::fromRequest($request);

            $designWorkData = (new StoreDesignWorkCommand($designWork, json_decode($request->salepersons_id), json_decode($request->materials)))->execute();

            return response()->success($designWorkData, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function update(int $id, Request $request): JsonResponse
    {
        //check if user's has permission
        abort_if(authorize('update', DesignWorkPolicy::class), Response::HTTP_FORBIDDEN, 'Need update permission for Design Work!');

        try {
            $designWork = DesignWorkMapper::fromRequest($request, $id);

            (new UpdateDesignWorkCommand($designWork, json_decode($request->salepersons_id), json_decode($request->materials)))->execute();

            return response()->success($designWork, 'success', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        //check if user's has permission
        abort_if(authorize('destroy', DesignWorkPolicy::class), Response::HTTP_FORBIDDEN, 'Need destroy permission for Design Work!');

        try {
            (new DeleteDesignWorkCommand($id))->execute();

            return response()->success($id, "Successfully Deleted", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }
}
