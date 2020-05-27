<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRequest;
use App\User;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;

class RegisterController extends ApiController
{
    use SendsPasswordResetEmails;



    public function register(Request $request)
    {
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $input['role'] = 'Пользователь';
        $user = User::create($input);

        $success['token'] =  $user->createToken('MyApp')->accessToken;
        $success['user'] =  $user->toArray();
        return $this->sendResponse($success, 'User register successfully.');
    }

    public function login(UserLoginRequest $request)
    {
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);

        $user = User::where('phone', $input['login'])->orWhere('email', $input['login'])->first();
        if($user != null || $user->password != bcrypt($input['password'])) {
            $success['token'] = $user->createToken('MyApp')->accessToken;
            $success['name'] = $user->toArray();
            return $this->sendResponse($success, 'User login successfully.');
        } else {
            return $this->sendError('Login Error.', ['Login isn\'t corrected']);
        }
    }

    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        return $this->sendError('Reset Error.', [trans($response)]);
    }

    protected function sendResetLinkResponse(Request $request, $response)
    {
        return $this->sendResponse("success", 'Message sent.');
    }

//    public function sendResetLinkEmail(Request $request)
//    {
//        $response = $this->broker()->sendResetLink(['email' => $request->get('email')]);
//        return
//    }

}
