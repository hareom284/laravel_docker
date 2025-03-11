<?php

namespace Src\Company\Document\Presentation\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Src\Company\Document\Application\UseCases\Commands\StoreWorkScheduleCommand;
use Src\Company\Document\Application\UseCases\Queries\FindWorkScheduleByIdQuery;
use Src\Company\Document\Application\UseCases\Commands\DeleteWorkScheduleCommand;
use Src\Company\Document\Application\UseCases\Queries\FindWorkScheduleByProjectIdQuery;

class WorkScheduleController extends Controller
{

    public function getWorkSchedules(int $projectId)
    {
        try {
            return response()->success((new FindWorkScheduleByProjectIdQuery($projectId))->handle(), 'success', Response::HTTP_CREATED);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function show($id)
    {
        try {
            return response()->success((new FindWorkScheduleByIdQuery($id))->handle(), 'success', Response::HTTP_CREATED);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'document_file' => ['required', 'array'],
                'document_file.*' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png']
            ]);
            $projectId = $request->project_id;
            $documentFiles = $request->file('document_file');

            $data = (new StoreWorkScheduleCommand($projectId, $documentFiles))->execute();
            return response()->success($data, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function delete($id)
    {
        try {
            (new DeleteWorkScheduleCommand($id))->execute();
            return response()->success($id, "Successfully Deleted", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }
}
