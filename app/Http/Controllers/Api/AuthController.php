<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeMessage;
use Tymon\JWTAuth\Facades\JWTAuth;

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

            $email = $request->get('email');

            Mail::to($email)->send(new WelcomeMessage($user));

            $user->save();

            //return successful response
            return response()->json([
                'user' => $user,
                'message' => 'Created',
                'status' => 'Success'
            ], 201);

        } catch (\Exception $e) {
            //return error message
            return response()->json([
                'message' => 'User Registration Failed!',
                'status' => 'Failed'
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

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['message' => 'Unauthorized'], 401);
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
            'message' => 'Successfully logged out',
            'status' => true
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
        return response()->json([
            'user' => auth()->user(),
            'status' => true
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

    /**
     * Request an email verification email to be sent.
     *
     * @param  Request  $request
     * @return Response
     */
    public function emailRequestVerification(Request $request)
    {
        if ( $request->user()->hasVerifiedEmail() ) {
            return response()->json('Email address is already verified.');
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json('Email request verification sent to '. Auth::user()->email);
    }

    /**
    * Verify an email using email and token from email.
    *
    * @param  Request  $request
    * @return Response
    */
    public function emailVerify(Request $request)
    {
        $this->validate($request, [
            'token' => 'required|string',
        ]);

        JWTAuth::getToken();
        JWTAuth::parseToken()->authenticate();

        if ( ! $request->user() ) {
            return response()->json('Invalid token', 401);
        }

        if ( $request->user()->hasVerifiedEmail() ) {
            return response()->json('Email address '.$request->user()->getEmailForVerification().' is already verified.');
        }

        $request->user()->markEmailAsVerified();

        return response()->json('Email address '. $request->user()->email.' successfully verified.');
    }

}
