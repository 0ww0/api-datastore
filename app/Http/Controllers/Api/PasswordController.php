<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
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

        if(! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email address',
                'error_code' => 400,
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Please check your email to reset your password.'
        ], 200);
    }

    /**
     * Create new P assword
     *
     * @bodyParam password string required The new password
     *
     * @param Request $request
     * @param $token
     * @return JsonResponse
     */
    public function recover(Request $request)
    {
        $this->validate($request, [
            'password' => 'required|min:8',
        ]);

        $token = $request->get('token');

        $user = User::newPasswordByResetToken($token, $request->input('password'));

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid password reset token',
                'error_code' => 400,
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Password has been changed.'
        ], 200);
    }

}
