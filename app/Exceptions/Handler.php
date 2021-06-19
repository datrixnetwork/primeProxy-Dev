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
                 return response(array('status' => $status,'code'=>$code, 'errors' => ['data' => $message],$code),404);

            break;
            case 'Exception':
                $status    = 'failed';
                $code      = ($exception->getCode() > 0? $exception->getCode() :401);
                $message   = $exception->getMessage();
                return response(array('status' => $status,'code'=>$code, 'errors' => ['data' => $message],$code),401);
            break;
            case 'Illuminate\Database\QueryException':
                $status    = 'failed';
                $code      = ($exception->getCode() > 0? $exception->getCode() :502);
                $message   = "Invalid Request";
                return response(array(['status' => $status,'code'=>$code, 'errors' => ['data' => $message],$code]),502);
                // return response(array(['status' => $status,'code'=>$code, 'errors' => ['data' => $message,'errorInfo'=>$errorInfo],$code]),502);
            break;
            case 'HttpClientException':
                $status    = 'failed';
                $code      = ($exception->getCode() > 0? $exception->getCode() :425);
                $message   = $exception->getMessage();
                return response(array('status' => $status,'code'=>$code, 'errors' => ['data' => $message],$code),425);
            break;
            case 'Illuminate\Auth\AuthenticationException':
                $status    = 'failed';
                $code      = ($exception->getCode() > 0? $exception->getCode() :425);
                $message   = $exception->getMessage();
                return response(array('status' => $status,'code'=>$code, 'errors' => ['data' => $message],$code),425);
            break;
            case 'AuthenticationException ':
                $status    = 'failed';
                $code      = ($exception->getCode() > 0? $exception->getCode() :403);
                $message   = $exception->getMessage();
                return response(array('status' => $status,'code'=>$code, 'errors' => ['data' => $message],$code),403);
            break;
            case 'ErrorException':
                $status    = 'failed';
                $code      = ($exception->getCode() > 0? $exception->getCode() :403);
                $message   = "Missing Param".str_replace('Undefined index:','',$exception->getMessage());
                return response(array('status' => $status,'code'=>$code, 'errors' => ['data' => $message],$code),403);
            break;
            default:
                $status    = 'failed';
                $code      = ($exception->getCode() > 0 ?$exception->getCode():501);
                $message   = $exception->getMessage();
                return response(array('status' => $status,'code'=>$code, 'errors' => ['data' => $message],$code),500);
            break;
        }

        return parent::render($request, $exception);
    }
}
