<?php

namespace Src\Company\Project\Presentation\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Src\Company\Project\Application\Mappers\ReviewMapper;
use Src\Company\Project\Application\UseCases\Commands\DeleteEventCommand;
use Src\Company\Project\Application\UseCases\Commands\StoreReviewCommand;
use Src\Company\Project\Application\UseCases\Queries\FindAllReviewQuery;
use Symfony\Component\HttpFoundation\Response;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\Project\Application\Requests\StoreReviewRequest;
use Src\Company\Project\Application\Policies\ReviewPolicy;

class ReviewController extends Controller
{
    public function index(): JsonResponse
    {
        abort_if(authorize('view', ReviewPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Review!');

        try {

            $events = (new FindAllReviewQuery())->handle();

            return response()->success($events, "success", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function show(int $id): JsonResponse
    {
        //check if user's has permission
        abort_if(authorize('view', ReviewPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Review!');

        try {
            return response()->json((new FindEventByIdQuery($id))->handle());
        } catch (UnauthorizedUserException $e) {

            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function store(StoreReviewRequest $request): JsonResponse
    {
        abort_if(authorize('store', ReviewPolicy::class), Response::HTTP_FORBIDDEN, 'Need store permission for Review!');

        try {

            $review = ReviewMapper::fromRequest($request);

            $reviewData = (new StoreReviewCommand($review))->execute();

            return response()->success($reviewData, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }
}
