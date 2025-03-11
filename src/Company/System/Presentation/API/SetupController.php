<?php
namespace Src\Company\System\Presentation\API;

use Illuminate\Http\Request;
use Src\Company\CompanyManagement\Domain\Services\XeroService;
use Src\Company\System\Application\Mappers\xeroCallbackMapper;
use Src\Company\System\Application\UseCases\Commands\SetupCommand;
use Src\Company\System\Application\UseCases\Commands\handleCallbackCommand;

class SetupController
{
    public function setup()
    {
        $authUrl = (new SetupCommand())->execute();
        return response()->json(['url' => $authUrl]);
    }

    public function handleCallback(Request $request)
    {
        try {
            $callback = (new xeroCallbackMapper())->fromRequest($request);
            (new handleCallbackCommand($callback))->execute();

            $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');
            return redirect()->away($frontendUrl . '/setting-tab?status=success');
        } catch (\Throwable $th) {
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');
            
            return redirect()->away($frontendUrl . '/setting-tab?status=failed');
        }
    }
}