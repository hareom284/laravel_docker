<?php

namespace Src\Company\StaffManagement\Presentation\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Symfony\Component\HttpFoundation\Response;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\StaffManagement\Application\Mappers\RankMapper;
use Src\Company\StaffManagement\Application\Policies\RankPolicy;
use Src\Company\StaffManagement\Application\UseCases\Commands\DeleteRankCommand;
use Src\Company\StaffManagement\Application\UseCases\Commands\StoreRankCommand;
use Src\Company\StaffManagement\Application\UseCases\Commands\UpdateRankCommand;
use Src\Company\StaffManagement\Application\UseCases\Queries\FindAllRankQuery;
use Src\Company\StaffManagement\Infrastructure\EloquentModels\StaffEloquentModel;
use Src\Company\StaffManagement\Application\Requests\StoreRankRequest;
// use Illuminate\Validation\Rule;

class RankController extends Controller
{

    public function index(): JsonResponse
    {
        //check if user's has permission
        // abort_if(authorize('view', RankPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Rank!');

        try {

            return response()->success((new FindAllRankQuery())->handle(), "Rank List", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function store(StoreRankRequest $request): JsonResponse
    {
        // abort_if(authorize('store', RankPolicy::class), Response::HTTP_FORBIDDEN, 'Need store permission for Rank!');

        try {

            $rank = RankMapper::fromRequest($request);

            $rankData = (new StoreRankCommand($rank))->execute();

            return response()->success($rankData, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function update(Request $request): JsonResponse
    {
        // abort_if(authorize('update', RankPolicy::class), Response::HTTP_FORBIDDEN, 'Need update permission for Rank!');

        try {

            foreach ($request->all() as $value) {

                $rankRequest = new Request();
                $rankRequest->merge([
                    'id' => $value['id'],
                    'commission_percent' => $value['commission_percent'],
                    'rank_name' => $value['rank_name'],
                    'tier' => $value['tier'],
                    'or_percent' => $value['or_percent']
                ]);

                $validatedData = $rankRequest->validate([
                    'rank_name' => 'required',
                    'commission_percent' => 'required',
                    'or_percent' => 'required',
                    'tier' => [
                        'required',
                        Rule::unique('ranks')->ignore($value['id']),
                    ],
                ]);

                if (!$validatedData) {
                    return response()->json($validatedData);
                }

                $rank = RankMapper::fromRequest($rankRequest, $rankRequest->id);

                (new UpdateRankCommand($rank))->execute();
            }

            return response()->success(null, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        //check if user's has permission
        // abort_if(authorize('destroy', RankPolicy::class), Response::HTTP_FORBIDDEN, 'Need destroy permission for Rank!');

        try {

            $hasRank = StaffEloquentModel::query()->with('user')->where('rank_id', $id)->get();

            if ($hasRank->count() > 0) {

                return response()->error($hasRank, "Rank Data is Already In Use", Response::HTTP_BAD_REQUEST);
            } else {

                (new DeleteRankCommand($id))->execute();

                return response()->success($id, "Deleted Successfully", Response::HTTP_OK);
            }
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }
}
