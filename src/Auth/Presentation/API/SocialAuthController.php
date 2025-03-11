<?php

namespace Src\Auth\Presentation\API;

use Src\Common\Infrastructure\Laravel\Controller;
use Src\Auth\Domain\Services\SocialAuthService;
use Illuminate\Http\Request;

class SocialAuthController extends Controller
{
    protected $socialAuthService;

    public function __construct(SocialAuthService $socialAuthService)
    {
        $this->socialAuthService = $socialAuthService;
    }

    public function redirectToProvider($provider)
    {

        return $this->socialAuthService->redirectToProvider($provider);
    }

    public function handleProviderCallback($provider)
    {
        return $this->socialAuthService->handleProviderCallbackService($provider);
    }
}
