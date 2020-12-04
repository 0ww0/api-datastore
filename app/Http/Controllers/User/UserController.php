<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Profile;
use App\Mail\WelcomeMessage;

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
        $data = User::with('profile', 'roles')->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Successfully get list',
            'data' => $data,
        ], 200);
    }

    public function create(Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
        ]);

        try {

            $user = new User;
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $plainPassword = $request->input('password');
            $user->password = app('hash')->make($plainPassword);
            $user->verification_token = Str::random(64);

            $email = $request->get('email');

            $user->save();

            $user->profile = new Profile;

            if($request->hasFile('image')){
                $extension = $request->file('image')->getClientOriginalExtension();
                $filename  = 'profile-photo-' . time() . '.' . $extension;
                $destination = '/images/avatar/';
                $path = $request->file('image')->move($destination, $filename);
                $user->profile->image = $path;
            }

            $user->profile()->save($user->profile);

            Mail::to($email)->send(new WelcomeMessage($user));

            return response()->json([
                'success' => true,
                'message' => 'Account created. Please verify via email.',
                'data' =>  $user
            ], 201);

        } catch(\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'User Registration Failed!',
                'error_code' => 409,
                'data' => $user
            ], 409);

        }
    }

    public function show($id)
    {
        $data = User::with('profile')->find($id);

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
        $data = User::find($id);
        $data->name = $request->input('name');

        if($request->hasFile('image')){
            $extension = $request->file('image')->getClientOriginalExtension();
            $filename  = 'profile-photo-' . time() . '.' . $extension;
            $destination = '/images/avatar/';
            $path = $request->file('image')->move($destination, $filename);
            $user->profile->image = $path;
        }

        $data->push();

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

    public function destroy($id)
    {
        $data = User::find($id);
        $data->roles()->detach();
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
        ], 200);
    }

}
