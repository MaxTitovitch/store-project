<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiAuthenticate
{
    public function handle($request, Closure $next, $isNeedVerify, $isAuthGet, $routeFor)
    {
        if($request->isMethod('get') && $isAuthGet == 'false') {
            return $next($request);
        }

        $user = auth('api')->user();
        if ($user == null) {
            return $this->sendErrorMessage('User isn\'t authenticated.');
        } else if ($isNeedVerify == "true" && $user instanceof MustVerifyEmail && !$user->hasVerifiedEmail()) {
            return $this->sendErrorMessage('User isn\'t verify email.');
        } else if ($routeFor == 'admin' && $user->role == 'Пользователь') {
            return $this->sendErrorMessage('User isn\'t admin.');
        }else if ($routeFor == 'main' && $user->role != 'Главный администратор') {
            return $this->sendErrorMessage('User isn\'t main admin.');
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
