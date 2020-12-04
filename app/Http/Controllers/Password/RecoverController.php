<?php

namespace App\Http\Controllers\Recover;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;

class RecoverController extends Controller
{
    /**
     * Create new Password
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
