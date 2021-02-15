<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\Utility\AuthService;
use Exception;
use App\Helpers;
use Illuminate\Cache\RateLimiter;

class AuthorizationHeader
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($this->hasTooManyRequests()) {
            sleep($this->limiter()->availableIn($this->throttleKey()) + 1);
            return $this->handle();
        }

        $this->limiter()->hit($this->throttleKey(),  env("THROTTLETIME", "60"));

        try{
            $authService =  new AuthService($request);

            if($authService->validPlatform == true){
                $data = [ 'client' =>$authService->clientInfo ];
                $request['ClientInfo'] = $data;
            }else{
                throw new Exception("Invalid request", 403);
            }

        }
        catch(Exception $ex){
            $code                       = $ex->getCode();
            $message                    = $ex->getMessage();
            $response                   = array($message);
            $errorResponse              = Helpers\Helper::MakeResponse('error') ;
            $errorResponse['errorCode'] = 401;
            $errorResponse['error']     = $response;
            return response(json_encode($errorResponse));
        }
        return $next($request);

    }

    protected function hasTooManyRequests()
    {
        return $this->limiter()->tooManyAttempts(
            $this->throttleKey(), env("THROTTLEHIT", "100") // <= max attempts per minute
        );
    }
    protected function limiter()
    {
        return app(RateLimiter::class);
    }
    protected function throttleKey()
    {
        return 'custom_api_request';
    }
}
