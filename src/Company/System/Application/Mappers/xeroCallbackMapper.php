<?php

namespace Src\Company\System\Application\Mappers;

use Illuminate\Http\Request;

class xeroCallbackMapper
{
    public static function fromRequest(Request $request): array
    {
        return [
            'code' => $request->code,
            'scope' => $request->scope,
            'state' => $request->state,
            'session' => $request->session,
        ];
    }
}