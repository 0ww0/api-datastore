<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\Profile;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MeController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Get user details.
     *
     * @param  Request  $request
     * @return Response
     */
    public function me()
    {
        $user = auth()->user();
        $user->profile = auth()->user()->profile;

        return response()->json([
            'success' => true,
            'message' => 'Retrive User info',
            'data' => $user,
        ], 200);
    }
}