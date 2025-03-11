<?php

namespace Src\Company\Project\Presentation\API;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Src\Common\Infrastructure\Laravel\Controller;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Src\Company\Project\Application\UseCases\Commands\UpdateScheduleMobileCommand;
use Src\Company\Project\Application\UseCases\Commands\UpdateScheduleStatusCommand;
use Src\Company\Project\Application\UseCases\Commands\UpdateAllScheduleStatusCommand;
use Src\Company\Document\Infrastructure\EloquentModels\RenovationSectionsEloquentModel;
use Src\Company\Project\Application\UseCases\Queries\FindScheduleDateByProjectIdMobileQuery;
use Src\Company\Project\Application\UseCases\Queries\FindAllRenovationItemSchedulesMobileQuery;
use Src\Company\Project\Application\UseCases\Queries\FindRenovationItemCountWithSectionMobileQuery;
use Src\Company\Project\Application\UseCases\Queries\FindAllRenovationItemSchedulesByDateMobileQuery;

class RenovationItemScheduleMobileController extends Controller
{
    public function index($projectId): JsonResponse
    {
        // check if user's has permission
        // abort_if(authorize('view', EventPolicy::class), Response::HTTP_FORBIDDEN, '403 Forbidden');

        try {
            $data['date'] = (new FindScheduleDateByProjectIdMobileQuery($projectId))->handle();  // to get min start date and max end date

            $data['data'] = (new FindAllRenovationItemSchedulesMobileQuery($projectId))->handle();  // to get data by document type and section

            $data['itemCount'] = (new FindRenovationItemCountWithSectionMobileQuery($projectId))->handle();  // to get reno item count

            return response()->success($data, 'success', Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {
            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function getSectionsByDate(Request $request, $projectId): JsonResponse
    {
        try {
            $data = (new FindAllRenovationItemSchedulesByDateMobileQuery((int) $projectId, $request->date))->handle();
            return response()->success($data,"success",Response::HTTP_OK);

        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function updateSchedule(Request $request): JsonResponse
    {
        // check if user's has permission
        // abort_if(authorize('update', EventPolicy::class), Response::HTTP_FORBIDDEN, '403 Forbidden');

        try {
            $scheduleRequests = $request->all();

            $scheduleData = (new UpdateScheduleMobileCommand($scheduleRequests))->execute();

            return response()->success($scheduleData, 'Success', Response::HTTP_OK);
        } catch (\DomainException $e) {
            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {
            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function updateStatus($id, Request $request): JsonResponse
    {
        try {
            $scheduleRequests = $request->all();

            $scheduleData = (new UpdateScheduleStatusCommand($scheduleRequests, $id))->execute();

            return response()->success($scheduleData, 'Success', Response::HTTP_OK);
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

            $scheduleData = (new UpdateAllScheduleStatusCommand($itemsIds, $isChecked))->execute();

            return response()->success($scheduleData, 'Success', Response::HTTP_OK);
        } catch (\DomainException $e) {
            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {
            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function uploadImages(Request $request)
    {
        $validated = $request->validate([
            'section_id' => 'required|exists:sections,id',
            'document_id' => 'required|exists:renovation_documents,id',
            'status' => 'required|in:inprogress,completed',
            'image' => 'required|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        $image = $request->file('image');

        $section = RenovationSectionsEloquentModel::where('section_id', $validated['section_id'])
            ->where('document_id', $request->document_id)
            ->firstOrFail();  // Use firstOrFail() to throw 404 if not found


        $project_progress = $validated['status'] === 'inprogress'
            ? 'project_progress_inprogress'
            : 'project_progress_completed';

        $path = "renovation_images/project_progress/section_{$request->section_id}/{$project_progress}";

        // Add media with custom directory structure
        $media = $section
            ->addMediaFromRequest('image')
            ->withCustomProperties(['progress_status' => $path])
            ->toMediaCollection($project_progress);

        $media = $section->getMedia($project_progress)->map(fn($item) => $item->toArray())->all();


        return response()->success(
            $media,
            'Images uploaded successfully',
            Response::HTTP_OK
        );
    }

    public function deleteImage(Request $request)
    {
        try {


            // Find media by ID
            $media = Media::findOrFail($request->photo_id);
            $section = RenovationSectionsEloquentModel::where('section_id', $request->section_id)
                ->where('document_id', $request->document_id)
                ->first();

            if ($media->model_id !== $section->id) {
                    return response()->json(['message' => 'Media does not belong to the specified section'], 403);
            }

            $media->delete();


            return response()->json([
                'message' => 'Image deleted successfully'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error deleting image',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getSectionImages(Request $request)
    {
        $project_progress = $request->status === 'inprogress'
        ? 'project_progress_inprogress'
        : 'project_progress_completed';

        $section = RenovationSectionsEloquentModel::where('document_id', $request->document_id)
            ->where('section_id', $request->section_id)
            ->first();

        $media = $section->getMedia($project_progress)->map(fn($item) => $item->toArray())->all();

        $data = [
            'section_id' => $section->id,
            'images' => $media,
        ];
        return response()->success($data, 'Success', Response::HTTP_OK);
    }
}
