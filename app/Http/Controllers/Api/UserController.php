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
        $data = User::paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Successfully get list',
            'data' => $data,
        ], 200);
    }

    public function create(Request $request)
    {
        $user = new User;
    }

    public function show($id)
    {
        $data = User::find($id);

        if(! $data) {
            return response()->json([
                'success' => false,
                'message' => 'Id not found',
                'error_code' => 204,
            ], 204);
        }

        return response()->json([
            'success' => true,
            'message' => 'Successfully get id',
            'data' => $data
        ], 200);
    }

    public function update(Request $request, $id)
    {

    }

    public function destroy($id)
    {
        $data = User::find($id);
        $data->delete();

        if(! $data) {
            return response()->json([
                'success' => false,
                'message' => 'Id not found',
                'error_code' => 204,
            ], 204);
        }

        return response()->json([
            'success' => true,
            'message' => 'Successfully delete id',
            'data' => $data
        ], 200);
    }

}
