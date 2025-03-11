<?php

namespace Src\Company\UserManagement\Presentation\API;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Src\Company\UserManagement\Application\Policies\UserPolicy;
use Src\Company\UserManagement\Domain\Model\ValueObjects\Password;
use Src\Company\UserManagement\Application\Requests\SurveyAnswerRequest;
use Src\Company\UserManagement\Application\Requests\UpdateProfileRequest;
use Src\Company\UserManagement\Application\UseCases\Commands\UpdateProfileCommand;
use Src\Company\UserManagement\Application\UseCases\Commands\FindSurveyAnswerCommand;
use Src\Company\UserManagement\Application\UseCases\Commands\StoreSurveyAnswerCommand;
use Src\Company\UserManagement\Application\UseCases\Commands\UpdateProfileMobileCommand;
use Src\Company\UserManagement\Application\UseCases\Commands\UpdateDeviceIdMobileCommand;
use Src\Company\UserManagement\Application\UseCases\Queries\FindSalepersonListMobileQuery;
use Src\Company\UserManagement\Application\UseCases\Queries\FindUserResourceByIdMobileQuery;

class UserMobileController extends Controller
{
    public function findUserInfoById($id)
    {
        abort_if(authorize('view', UserPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for User!');

        try {
            return response()->success((new FindUserResourceByIdMobileQuery($id))->handle(), "User", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function getSalepersonList()
    {
        abort_if(authorize('view_salesperson', UserPolicy::class), Response::HTTP_FORBIDDEN, 'Need view_saleperson permission for User!');

        try {

            return response()->success((new FindSalepersonListMobileQuery())->handle(), "Saleperson Lists", Response::HTTP_OK);

        } catch (UnauthorizedUserException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function updateProfile(int $id, UpdateProfileRequest $request): JsonResponse
    {
        try {

            $user = $request->all();

            if ($request->has('password') && $request->input('password')) {

                $password = new Password($request->input('password'), $request->input('password_confirmation'));

                $updatedUser = (new UpdateProfileMobileCommand($user, $password, $id))->execute();
            } else {
                // Perform the update without updating the password
                $updatedUser = (new UpdateProfileMobileCommand($user, null, $id))->execute();
            }

            if ($updatedUser->profile_pic) {
                $profile_pic_file_path = 'profile_pic/' . $updatedUser->profile_pic;

                $profile_pic_image = Storage::disk('public')->get($profile_pic_file_path);

                $updatedUser->profile_pic = base64_encode($profile_pic_image);
            }

            return response()->success($updatedUser, 'success', Response::HTTP_OK);
        } catch (\Exception $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function updateDeviceId(Request $request)
    {
        try {

            $requestData = $request->all();

            (new UpdateDeviceIdMobileCommand($requestData))->execute();

            return response()->success('', 'Device Id Update Successful !', Response::HTTP_OK);

        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function survey(Request $request)
    {
        try {
            $floorPlanPath = null;
            Log::info('request data', $request->all());
            if ($request->hasFile('floor_plan')) {
                $fileName =  time() . '.' . $request->floor_plan->getClientOriginalExtension();
                $filePath = 'floor_plan/' . $fileName;
                Storage::disk('public')->put($filePath, file_get_contents($request->floor_plan));
                $floorPlanPath = $fileName;
            }
            $surveyData = [
                'property_type' => $request->property_type,
                'kitchen_work' => $request->kitchen_work,
                'preferred_style' => $request->preferred_style,
                'floor_plan' => $floorPlanPath,
            ];

            $survey = (new StoreSurveyAnswerCommand($surveyData))->execute();

            return response()->success($survey, 'Success', Response::HTTP_OK);

        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function getSurvey()
    {
        try {
            $survey = (new FindSurveyAnswerCommand())->execute();
        Log::info('getSurvey', [$survey]);

            return response()->success($survey, 'Success', Response::HTTP_OK);

        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }
}
