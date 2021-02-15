<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
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
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        $obj = get_class($exception);

        switch($obj){
            case 'Throwable':
                 $status    = 'failed';
                 $code      = ($exception->getCode() > 0? $exception->getCode() :404);
                 $message   = $exception->getMessage();
                 return response()->json(['status' => $status,'code'=>$code, 'errors' => ['data' => $message]], $code);

            break;
            case 'Exception':
                $status    = 'failed';
                $code      = ($exception->getCode() > 0? $exception->getCode() :401);
                $message   = $exception->getMessage();
                return response()->json(['status' => $status,'code'=>$code, 'errors' => ['data' => $message]], $code);
            break;
            case 'Illuminate\Database\QueryException':
                $status    = 'failed';
                $code      = ($exception->getCode() > 0? $exception->getCode() :404);
                $message   = $exception->getMessage();
                $errorInfo = $exception->getPrevious()->errorInfo;
                return response()->json(['status' => $status,'code'=>$code, 'errors' => ['data' => $message,'errorInfo'=>$errorInfo]], $code);
            break;
            case 'HttpClientException':
                $status    = 'failed';
                $code      = ($exception->getCode() > 0? $exception->getCode() :425);
                $message   = $exception->getMessage();
                return response()->json(['status' => $status,'code'=>$code, 'errors' => ['data' => $message]], $code);
            break;
            case 'InvalidArgumentException':
                $status    = 'failed';
                $code      = ($exception->getCode() > 0? $exception->getCode() :425);
                $message   = $exception->getMessage();
                return response()->json(['status' => $status,'code'=>$code, 'errors' => ['data' => $message]], $code);
            break;

            default:
                $status    = 'failed';
                $code      = ($exception->getCode() > 0 ?$exception->getCode():501);
                $message   = $exception->getMessage();
                return response()->json(['status' => $status,'code'=>$code, 'errors' => ['data' => $message]], $code);
            break;
        }

        return parent::render($request, $exception);
    }
}
