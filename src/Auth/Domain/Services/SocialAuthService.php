<?php

namespace Src\Auth\Domain\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Laravel\Socialite\Facades\Socialite;
use Src\Auth\Domain\Repositories\AuthRepositoryInterface;
use Src\Auth\Domain\Resources\AuthResource;
use Src\Company\CustomerManagement\Application\Mappers\CustomerMapper;
use Src\Company\CustomerManagement\Application\UseCases\Commands\StoreCustomerCommandMobile;
use Src\Company\CustomerManagement\Presentation\API\CustomerMobileController;
use Src\Company\UserManagement\Application\Mappers\UserMapper;
use Src\Company\UserManagement\Application\UseCases\Commands\StoreUserCommandMobile;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserMetaEloquentModel;
use Symfony\Component\HttpFoundation\Response;

class SocialAuthService
{
    private $authInterFace;

    public function __construct(AuthRepositoryInterface $authRepository)
    {
        $this->authInterFace = $authRepository;
    }

    public function redirectToProvider($provider)
    {
        if (!in_array($provider, config('services.allowed_providers'))) {
            return response()->json(['error' => 'Provider not supported'], 404);
        }

        return Socialite::driver($provider)->stateless()->redirect();

    }

    public function handleProviderCallbackService($provider)
    {
        try {

            $message = null;

            $socialiteUser = Socialite::driver($provider)->stateless()->user();

            DB::beginTransaction();

            $userExists = UserEloquentModel::where(
                'email',
                $socialiteUser->getEmail(),
            )->exists();

            if ($userExists) {
                $user = UserEloquentModel::where(
                    'email',
                    $socialiteUser->getEmail(),
                )->first();
                // $message = $action_type == 'signUp' ? ''
            } else {
                $userInputData = [
                    'first_name' => $socialiteUser->user['given_name'] ?? $socialiteUser->user['name'],
                    'last_name' => $socialiteUser->user['family_name'] ?? " ",
                    'contact_no' => '',
                    'email' => $socialiteUser->getEmail(),
                    'generatePassword' => false,
                    'role_ids' => json_encode([5]),
                    'saleperson_ids' => [],
                    'source' => '',
                    'budget' => '',
                    'id_milestone_id' => 1,
                    'key_collection' => now()->toDateString(),
                    'next_meeting' => now()->toDateString(),
                ];
                $request = Request::create('/dummy', 'POST', $userInputData);
                $user = UserMapper::fromRequest($request);
                $roleIds = ['5'];
                $salespersonIds = [];
                $password = null;
                $userData = (new StoreUserCommandMobile($user, $password, $roleIds, $salespersonIds))->execute();

                $user = UserEloquentModel::where('email', $userInputData['email'])->first();

                $customer = CustomerMapper::fromRequest($request, null, $userData->id);

                $customerData = (new StoreCustomerCommandMobile($customer, $salespersonIds))->execute();

                UserMetaEloquentModel::create([
                    'user_id' => $user->id,
                    'name' => 'social_'.$provider.'_id',
                    'val' => $socialiteUser->getId()
                ]);
            }

            $data['token'] = $user->createToken('API Token')->plainTextToken;
            $data['user'] = new AuthResource($user);
            DB::commit();


            return redirect()->away("pspace://auth/{$provider}/callback?token=" . urlencode($data['token']) . "&user=" . urlencode(json_encode($data['user'])));


        } catch (\Exception $e) {
            DB::rollBack();
            logger('Exception in item: ',[$e->getTrace()]);
            return response()->json(['error' => 'Invalid credentials provided.'], 422);
        }
    }
}
