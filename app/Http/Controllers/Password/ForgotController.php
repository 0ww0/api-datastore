<?php

namespace App\Http\Controllers\Password;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Mail\PasswordReset;

class ForgotController extends Controller
{
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
}
