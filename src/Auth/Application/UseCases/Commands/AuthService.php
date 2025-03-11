<?php

namespace Src\Auth\Application\UseCases\Commands;

use Src\Auth\Application\Requests\StoreLoginRequest;
use Src\Auth\Domain\Repositories\AuthRepositoryInterface;
use Src\Common\Infrastructure\Laravel\Notifications\BcNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Src\Auth\Domain\Mail\VerifyEmail;

class AuthService
{
    private AuthRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = app()->make(AuthRepositoryInterface::class);
    }


    public function Login(StoreLoginRequest $request)
    {
        $user = $this->repository->login($request);

        /***
         *  first thing here check user exists or not
         *  then check email is verified or not if verified then go another step
         *  auth attempt if email verified then sent notification inside dashboard
         *  if incorrect email and password get invalid message
         */
        if ($user) {
            //this check verify email or not
            if (!$user->email_verified_at) {
                $error = "Please Verify your email";
                return ["errorMessage" => $error, "isCheck" => false];
            }

            if (auth()->attempt([
                "email" => request('email'),
                "password" => request("password")
            ])) {
                $user->notify(new BcNotification(['message' => 'Welcome ' . $user->name . ' !', 'from' => "", 'to' => "", 'type' => "success"]));
                return ["errorMessage" => "Successfully", "isCheck" => true];
            } else {
                $error = "Invalid Login Credential";
                return ["errorMessage" => $error, "isCheck" => false];
            }
        }

        // if not fail log in
        else {

            $error = "Invalid Login Credential";
            return ["errorMessage" => $error, "isCheck" => false];
        }
    }

    /***i
     *  this is logout function that logout user and remove session
     *  from page builder for not accessiable for users
     *  @params null
     *  @return void
     */
    function Logout()
    {
        // Logout the authenticated user
        Auth::logout();

        // Remove the 'phpb_logged_in' session to revoke page builder access
        session()->remove('phpb_logged_in');
    }


    function registerB2CUser($register)
    {
        $user =  $this->repository->b2cRegister($register);
        Mail::to(request('email'))->send(new VerifyEmail($user));
    }
}
