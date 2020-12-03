<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    /**
    * Get a JWT via given credentials.
    *
    * @param  Request  $request
    * @return Response
    */
    public function login(Request $request)
    {
          //validate incoming request
        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only(['email', 'password']);

        $token = auth()->attempt($credentials);

        if (! $token ) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
                'error_code' => 401,
            ], 401);
        }

        return $this->respondWithToken($token);
    }
}