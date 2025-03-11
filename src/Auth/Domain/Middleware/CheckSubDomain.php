<?php

namespace Src\Auth\Domain\Middleware;

use Closure;

class CheckSubDomain
{
    public function handle($request, Closure $next)
    {

        // dd(is_null(is_null(auth()->user()->storage_limit)));
        if (is_null(auth()->user()->oraganization_id)) {
            return redirect('http://' . $organization->organization->name . '.' . request()->getHost() . '/c/organizationaadmin');
        }

        return $next($request);
    }
}
