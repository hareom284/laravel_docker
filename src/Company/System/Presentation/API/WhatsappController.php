<?php

namespace Src\Company\System\Presentation\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Symfony\Component\HttpFoundation\Response;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\System\Application\UseCases\Commands\SendTemplateMessageCommand;
use Src\Company\System\Domain\Services\WhatsappNotification;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;

class WhatsappController extends Controller
{
    public function sendTemplateMessage()
    {
        try {
            // $template_name = "hello_world";
            // $language_code = "en_us";
            // $to = "959421041709";
            // $name = "David";
            // $document_type = "Quotation";
            // $file_url = "https://pdfobject.com/pdf/sample.pdf";
            // $total_amount = 10000;
            // $response = (new SendTemplateMessageCommand($template_name, $language_code, $to, $name, $file_url, $document_type, $total_amount))->execute();
            // return response()->success($response, "Success", Response::HTTP_CREATED,);
            $user = UserEloquentModel::find(1);
            $user->notify(new WhatsappNotification(
                'hello_world',
                'en_us',
                '959421041709',
                'David',
                'https://pdfobject.com/pdf/sample.pdf',
                'Quotation',
                '100000'
            ));
            return response()->success($user, "Success", Response::HTTP_CREATED,);
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

    }
}
