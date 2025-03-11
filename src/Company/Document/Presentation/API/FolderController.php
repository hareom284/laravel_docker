<?php

namespace Src\Company\Document\Presentation\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Src\Company\Document\Application\Mappers\FolderMapper;
use Src\Company\Document\Application\UseCases\Commands\StoreFolderCommand;
use Src\Company\Document\Application\UseCases\Commands\DeleteFolderCommand;
use Src\Company\Document\Application\UseCases\Commands\UpdateFolderCommand;
use Src\Company\Document\Application\UseCases\Queries\FindFolderByIdQuery;
use Symfony\Component\HttpFoundation\Response;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\Document\Application\Requests\StoreFolderRequest;
use Src\Company\Document\Application\UseCases\Queries\FindFoldersByProjectIdQuery;
use Src\Company\Document\Domain\Repositories\FolderRepositoryInterface;


class FolderController extends Controller
{
    private $folderInterFace;

    public function __construct(FolderRepositoryInterface $folderRepository)
    {
        $this->folderInterFace = $folderRepository;
    }

    public function index(Request $request): JsonResponse
    {
        try {

            $filters = $request;

            $folders = $this->folderInterFace->getFolders($filters);

            return response()->success($folders,'success',Response::HTTP_OK);

        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function folderByProjectId($projectId)
    {
        try {

            $folders = (new FindFoldersByProjectIdQuery(intval($projectId)))->handle();

            return response()->success($folders,'success',Response::HTTP_OK);

        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {

            $folder = (new FindFolderByIdQuery($id))->handle();

            return response()->success($folder,'success',Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function store(StoreFolderRequest $request): JsonResponse
    {
        try {
            $folder = FolderMapper::fromRequest($request);

            $folderData = (new StoreFolderCommand($folder))->execute();

            return response()->success($folderData,'success', Response::HTTP_CREATED);

        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);

        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function update(int $id, Request $request): JsonResponse
    {
        try {
            $folder = FolderMapper::fromRequest($request, $id);

            (new UpdateFolderCommand($folder))->execute();

            return response()->success($folder,'success',Response::HTTP_OK);

        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);

        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            (new DeleteFolderCommand($id))->execute();

            return response()->success($id,"Successfully Deleted", Response::HTTP_OK);

        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

}