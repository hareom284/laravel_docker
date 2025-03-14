<?php

namespace Src\Common\Infrastructure\Laravel\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Exception;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Src\Company\System\Domain\Mail\ExceptionOccured;


class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Exception $e) {
            $this->sendEmail($e);
        });
    }


    /**
     * Exception handling send mail to user when
     * error happened
     * @return
     */
    public function sendEmail(Exception $exception)
    {

       try {

            $content['message'] = $exception->getMessage();
            $content['file'] = $exception->getFile();
            $content['line'] = $exception->getLine();
            $content['trace'] = $exception->getTrace();

            $content['url'] = request()->url();
            $content['body'] = request()->all();
            $content['ip'] = request()->ip();

            // Mail::to(trans('auth.support_email'))->send(new ExceptionOccured($content));

        } catch (Exception $exception) {
            Log::error($exception);
        }
    }
}
