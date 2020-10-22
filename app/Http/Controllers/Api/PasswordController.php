<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Mail\PasswordReset;

class PasswordController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
    * Send new Password Request
    *
    * @bodyParam email string required The email
    *
    * @param Request $request
    * @return JsonResponse
    */
    public function forgot(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|exists:users'
        ]);

        $user = User::byEmail($request->input('email'));

        Mail::to($user)->send(new PasswordReset($user));

        return response()->json(['data' => ['message' => 'Please check your email to reset your password.']]);
    }

    /**
     * Create new P assword
     *
     * @bodyParam password string required The new password
     *
     * @param Request $request
     * @param $token
     * @return JsonResponse
     * @throws ValidationException
     */
    public function recover(Request $request)
    {
        $this->validate($request, [
            'password' => 'required|min:8',
        ]);

        $token = $request->get('token');

        $user = User::newPasswordByResetToken($token, $request->input('password'));

        if ($user) {
            return response()->json(['data' => ['message' => 'Password has been changed.']]);
        } else {
            return response()->json(['data' => ['message' => 'Invalid password reset token']], 400);
        }
    }

}
