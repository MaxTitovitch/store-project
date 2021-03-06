<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRequest;
use App\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends ApiController
{
    use ResetsPasswords, SendsPasswordResetEmails {
        ResetsPasswords::broker insteadof SendsPasswordResetEmails;
        SendsPasswordResetEmails::broker insteadof ResetsPasswords;
        ResetsPasswords::broker as brokerReset;
        SendsPasswordResetEmails::broker as broker;

        ResetsPasswords::credentials insteadof SendsPasswordResetEmails;
        SendsPasswordResetEmails::credentials insteadof ResetsPasswords;
        ResetsPasswords::credentials as credentialsReset;
        SendsPasswordResetEmails::credentials as credentials;
    }

    public function verifySend(Request $request){
        $user = auth('api')->user();
        if ($user->hasVerifiedEmail()) {
            return $this->sendError('Verify error', 'Email is verified.');
        }

        $user->sendEmailVerificationNotification();

        return $this->sendResponse('Success', 'Message sent successfully.');
    }

    public function verifySave(Request $request){
        $input = $request->all();
        $user = User::find($input['id']);
        if ($user == null) {
            return $this->sendError('Verify error', 'Users isn\' corrected.');
        }
        if ($user->hasVerifiedEmail()) {
            return $this->sendError('Verify error', 'Email is verified.');
        }
        if (! hash_equals( $input['token'], sha1($user->getEmailForVerification()))) {
            return $this->sendError('Verify error', 'Users data isn\'t corrected.');
        }
        if ($user->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        $success['token'] =  $user->createToken('MyApp')->accessToken;
        $success['user'] =  $user->toArray();
        return $this->sendResponse($success, 'Email verified successfully.');
    }

    public function register(UserRequest $request)
    {
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $input['role'] = 'Пользователь';
        $input['slug'] = str_slug($input['email']);
        $user = User::create($input);
        $user->sendEmailVerificationNotification();

        $success['token'] =  $user->createToken('MyApp')->accessToken;
        $success['user'] =  $user->toArray();
        return $this->sendResponse($success, 'Users register successfully.');
    }

    public function login(UserLoginRequest $request)
    {
        $input = $request->all();

        $user = User::where('phone', $input['login'])->orWhere('email', $input['login'])->first();


        if($user != null && Hash::check($input['password'], $user->password) ) {
            $success['token'] = $user->createToken('MyApp')->accessToken;
            $success['user'] = $user->toArray();
            return $this->sendResponse($success, 'Users login successfully.');
        } else {
            return $this->sendError('Login Error', 'Login isn\'t corrected');
        }
    }

    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        return $this->sendError('Reset sending Error', [trans($response)]);
    }

    protected function sendResetLinkResponse(Request $request, $response)
    {
        return $this->sendResponse("Success", 'Message sent.');
    }

    protected function sendResetFailedResponse(Request $request, $response)
    {
        return $this->sendError('Reset Error', [trans($response)]);
    }

    protected function sendResetResponse(Request $request, $response)
    {
        return $this->sendResponse($response, 'Password reset.');
    }

    public function reset(Request $request)
    {
        $request->validate($this->rules(), $this->validationErrorMessages());
        $response = $this->brokerReset()->reset(
            $this->credentialsReset($request), function ($user, $password) {
                $this->resetPassword($user, $password);
            }
        );

        $user = User::where('email', $request['email'])->first();
        if($user != null) {
            $success['token'] = $user->createToken('MyApp')->accessToken;
            $success['user'] = $user->toArray();
            return $this->sendResponse($success, 'Password reset successfully.');
        } else {
            return $this->sendError('Login Error', ['Login isn\'t corrected']);
        }
    }
}
