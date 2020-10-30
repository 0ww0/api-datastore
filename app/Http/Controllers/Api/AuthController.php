<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Profile;
use App\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\WelcomeMessage;


class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Store a new user.
     *
     * @param  Request  $request
     * @return Response
    */

    public function register(Request $request)
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
            $user->profile->image = '/default/avatar.png';

            $role = 'admin';

            $attach_role = Role::where('name', $role)->get('id')->first();

            $user->roles()->attach($attach_role);

            // if($request->hasFile('image')){
            //     $extension = $request->file('image')->getClientOriginalExtension();
            //     $filename  = 'profile-photo-' . time() . '.' . $extension;
            //     $destination = './images/avatar/';
            //     $path = $request->file('image')->move($destination, $filename);
            //     $user->profile->image = $path;
            // }

            $user->profile()->save($user->profile);

            Mail::to($email)->send(new WelcomeMessage($user));

            return response()->json([
                'success' => true,
                'message' => 'Account created. Please verify via email.',
                'data' =>  $user
            ], 201);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'User Registration Failed!',
                'error_code' => 409,
            ], 409);

        }

    }

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

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
    */
    public function logout()
    {
        auth()->logout();

        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out',
        ], 200);
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

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

}
