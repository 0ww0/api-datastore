<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
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

    public function index()
    {
        $users = User::paginate(10);
        return response()->json($users);
    }

    public function create(Request $request)
    {
        $user = new User;
    }

    public function show($id)
    {
        $users = User::find($id);
        return response()->json($user);
    }

}
