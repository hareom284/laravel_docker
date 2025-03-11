<?php

namespace Src\Auth\Presentation\API;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;
use Src\Auth\Application\Requests\RecoverPasswordRequest;
use Src\Auth\Application\Requests\StoreLoginRequest;
use Src\Auth\Application\Requests\UpdatePasswordRequest;
use Src\Auth\Application\UseCases\Commands\ChangePasswordCommand;
use Src\Auth\Application\UseCases\Commands\UpdatePasswordCommand;
use Src\Auth\Application\UseCases\Queries\FindUserByEmailQuery;
use Src\Auth\Domain\Mail\RecoverPasswordEmail;
use Src\Auth\Domain\Repositories\AuthRepositoryInterface;
use Src\Auth\Domain\Resources\AuthResource;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\UserManagement\Application\Mappers\UserMapper;
use Src\Company\UserManagement\Domain\Model\ValueObjects\Password;
use Symfony\Component\HttpFoundation\Response;;
use Src\Auth\Application\Requests\CreateCustomerRequest;
use Src\Company\CustomerManagement\Application\Mappers\CustomerMapper;
use Src\Company\CustomerManagement\Application\UseCases\Commands\StoreCustomerCommandMobile;
use Src\Company\CustomerManagement\Presentation\API\CustomerMobileController;
use Src\Company\UserManagement\Application\UseCases\Commands\StoreUserCommandMobile;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;

class AuthController extends Controller
{
    private $authInterFace;

    public function __construct(AuthRepositoryInterface $authRepository)
    {
        $this->authInterFace = $authRepository;
    }

    public function login(StoreLoginRequest $request): JsonResponse
    {
        try {
            $email = $request->get('email');

            $password = $request->get('password');

            $credentials = ['email' => strtolower($email), 'password' => $password];

            $user = $this->authInterFace->login($credentials);

            if ($user) {
                return response()->success($user, 'Successfully Authenticated', Response::HTTP_OK);
            } else {
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect']
                ]);
            }
        } catch (AuthenticationException) {
            return response()->error(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function logout(): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();

        // $user->tokens()->delete();
        $user->currentAccessToken()->delete();

        return response()->success(null, 'Successfully Logged Out', Response::HTTP_OK);
    }

    public function recoverPassword(RecoverPasswordRequest $request)
    {
        $requestEmail = $request->email;
        $key = 'recover-password-attempts:' . $requestEmail;
        $maxAttempts = 3;
        $decayMinutes = 60;

        // Check if the request exceeds the maximum attempts
        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            return response()->error(['error' => 'Too many attempts. Please try again later.'], Response::HTTP_TOO_MANY_REQUESTS);
        }

        // Increment the number of attempts
        RateLimiter::hit($key, $decayMinutes * 60);

        $token = Str::random(60);

        $feUrl = config('app.FRONTEND_URL');

        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => now()
        ]);

        $resetLink = $feUrl . '/reset-password?token=' . $token;

        Mail::to($request->email)->send(new RecoverPasswordEmail($resetLink));

        return response()->success(null, 'Password reset link has been sent to your email.', Response::HTTP_OK);
    }

    public function checkToken(Request $request)
    {
        $tokenExists = DB::table('password_reset_tokens')->where('token', $request->token)->exists();

        return response()->success($tokenExists, 'Success', Response::HTTP_OK);
    }

    public function resetPassword(Request $request)
    {
        $password = new Password($request->input('password'), $request->input('password_confirmation'));

        $token = DB::table('password_reset_tokens')->where('token', $request->token)->first();

        $tokenEmail = $token->email;

        $userExists = DB::table('users')->where('email', $tokenEmail)->exists();

        if ($userExists) {
            $user = (new ChangePasswordCommand($tokenEmail, $password))->execute();

            DB::table('password_reset_tokens')->where('token', $request->token)->delete();

            return response()->success($user, 'Success', Response::HTTP_OK);
        } else {
            return response()->error(['error' => 'Token Invalid'], Response::HTTP_BAD_REQUEST);
        }
    }

    public function getPermissions()
    {
        try {
            $permissions = $this->authInterFace->getPermissionsByUserRole();

            return response()->success($permissions, 'Success', Response::HTTP_OK);
        } catch (UnauthorizedUserException $error) {
            return response()->error($error->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        try {
            $user = $request->user();

            $password = new Password($request->new_password, $request->new_password_confirmation);

            (new UpdatePasswordCommand($user->id, $password))->execute();

            return response()->success(null, 'Successfully Update Password', Response::HTTP_OK);
        } catch (UnauthorizedUserException $error) {
            return response()->error($error->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function createCustomer(CreateCustomerRequest $request)
    {

        try {

            DB::beginTransaction();

            $userInputData = [
                'first_name' => $request['first_name'],
                'last_name' => $request['last_name'],
                'contact_no' => $request['contact_no'],
                'name_prefix' => $request['name_prefix'] ?? 'Mr',
                'prefix' => $request['prefix'] ?? '65',
                'email' => $request['email'],
                'generatePassword' => false,
                'password' => $request['password'],
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
            $password = $request['password'];

            $userData = (new StoreUserCommandMobile($user, $password, $roleIds, $salespersonIds))->execute();

            $user = UserEloquentModel::where('email', $userInputData['email'])->first();

            $customer = CustomerMapper::fromRequest($request, null, $userData->id);

            $customerData = (new StoreCustomerCommandMobile($customer, $salespersonIds))->execute();

            $data['token'] = $user->createToken('API Token')->plainTextToken;
            $data['user'] = new AuthResource($user);

            DB::commit();

            return response()->success($data, 'Successfully Authenticated', Response::HTTP_OK);

        } catch (\Exception $error) {
            DB::rollback();
            logger('item', [$error->getMessage()]);
            return response()->json(['error' => 'Invalid credentials provided.'], 422);
        }
    }
}
