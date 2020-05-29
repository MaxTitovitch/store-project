<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;

class ApiAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $isNeedVerify)
    {
        $user = auth('api')->user();
        if ($user == null){
            return $this->sendErrorMessage('User isn\'t authenticated') ;
        } else if($isNeedVerify == "true" && $user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail()) {
            return $this->sendErrorMessage('User isn\'t verify email');
        }

        return $next($request);
    }

    protected function sendErrorMessage($message, $code = 404){
        $response = [
            'success' => false,
            'message' => $message,
        ];
        return response()->json($response, $code);
    }
}
