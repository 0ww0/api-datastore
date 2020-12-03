<?php

namespace App\Http\Controllers\Email;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class VerifyController extends Controller
{
    /**
     * Verify User
     *
     * @queryParam token required The token
     *
     * @param String $token
     * @return JsonResponse
     * @throws Exception
     */
    public function verify(Request $request)
    {
        $token = $request->get('token');
        $user = User::verifyByToken($token);

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid verification token',
                'error_code' => 400,
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Account has been verified'
        ], 200);
    }
}
