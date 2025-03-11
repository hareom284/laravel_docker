<?php

namespace Src\Auth\Domain\Middleware;

use Illuminate\Contracts\Session\Session;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;
use Illuminate\Session\Middleware\StartSession as FrameworkStartSession;

class CustomSession extends FrameworkStartSession {
    protected function addCookieToResponse(Response $response, Session $session)
    {
        // Maybe use something a bit more sophisticated here like e.g. detecting only the buggy versions
        $userAgent = request()->server('HTTP_USER_AGENT');

        if (Str::contains($userAgent, 'Safari') && !Str::contains($userAgent, 'Chrome')) {
           config([ 'session.same_site' => 'none' ]);
        }
        else {
            // Configure session for other browsers
            config(['session.same_site' => 'lax']);
        }
        return parent::addCookieToResponse($response, $session);
    }
}
