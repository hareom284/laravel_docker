<?php

namespace Src\Company\CustomerManagement\Presentation\API;

use Illuminate\Http\Request;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Symfony\Component\HttpFoundation\Response;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\CustomerManagement\Application\Mappers\CheckListMapper;
use Src\Company\CustomerManagement\Application\UseCases\Commands\CompleteCheckListCommand;
use Src\Company\CustomerManagement\Application\UseCases\Commands\DeleteCheckListCommand;
use Src\Company\CustomerManagement\Application\UseCases\Commands\StoreCheckListCommand;
use Src\Company\CustomerManagement\Application\UseCases\Queries\FindCheckListByCustomerIdQuery;
use Src\Company\CustomerManagement\Infrastructure\EloquentModels\CustomerEloquentModel;
use Src\Company\CustomerManagement\Application\Policies\CheckListPolicy;

class CheckListItemsController extends Controller
{

    public function checkListByCustomerId($id)
    {
        //check if user's has permission
        abort_if(authorize('view', CheckListPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Check List!');

        try {
            return response()->success((new FindCheckListByCustomerIdQuery($id))->handle(), "Check Lists", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function create(Request $request)
    {
        //check if user's has permission
        abort_if(authorize('store', CheckListPolicy::class), Response::HTTP_FORBIDDEN, 'Need store permission for Check List!');

        try {

            $customer_info = CustomerEloquentModel::where('user_id', $request->customer_id)->first();

            $request->merge(['customer_id' => $customer_info->id]);

            $check_list = CheckListMapper::fromRequest($request);

            $checkListData = (new StoreCheckListCommand($check_list))->execute();

            return response()->success($checkListData, "Success", Response::HTTP_CREATED);
        } catch (\DomainException $domainException) {
            return response()->json(['error' => $domainException->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function destroy(int $id)
    {
        //check if user's has permission
        abort_if(authorize('destroy', CheckListPolicy::class), Response::HTTP_FORBIDDEN, 'Need destroy permission for Check List!');

        try {
            (new DeleteCheckListCommand($id))->execute();

            return response()->success($id, "Deleted Successfully", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function completeCheck(Request $request, int $id)
    {
        abort_if(authorize('update', CheckListPolicy::class), Response::HTTP_FORBIDDEN, 'Need update permission for Check List!');

        try {

            $check_list = CheckListMapper::fromRequest($request);

            $checkListData = (new CompleteCheckListCommand($check_list, $id))->execute();

            return response()->success($checkListData, "Check List Status Change", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }
}
