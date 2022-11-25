<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    public function __construct()
    {
        //
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $data = $request->only(
            'name',
            'email',
            'password',
            'confirm_password'
        );

        // validate request data
        $validator = Validator::make($data, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:4|max:100',
            'confirm_password' => 'required|string|min:4|max:100|same:password',
        ]);

        // send failed response if request is not valid
        if( $validator->fails() )
        {
            return response()->json([
                'error' => $validator->errors()
            ], Response::HTTP_UNAUTHORIZED);
        }

        // create new user, for valid request
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);

        if( $user->save() )
        {
            //user created, return success response
            return response()->json([
                'success' => true,
                'message' => 'User created successfully.',
                'data' => $user
            ], Response::HTTP_OK);
        }

        //user not created, return failure response
        return response()->json([
            'success' => false,
            'message' => 'Sorry, user can not be created.'
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function login(Request $request): \Illuminate\Http\JsonResponse
    {
        $credentials = $request->only('email', 'password');

        // validate credentials
        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required|string|min:4|max:100'
        ]);

        // send failure response if request is not valid
        if( $validator->fails() )
        {
            return response()->json([
                'error' => $validator->errors()
            ], Response::HTTP_UNAUTHORIZED);
        }

        // request is validated
        // clear token
        $jwtToken = null;
        try
        {
            if( ! $jwtToken = JWTAuth::attempt($credentials) )
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Email or Password',
                ], Response::HTTP_UNAUTHORIZED);
            }
        }
        catch(JWTException $exception)
        {
            return response()->json([
                'success' => false,
                'message' => 'Could not create token.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // token created, return with success response and jwt token
        return response()->json([
            'success' => true,
            'token' => $jwtToken,
        ]);
    }

    public function logout(Request $request): \Illuminate\Http\JsonResponse
    {
        try
        {
            JWTAuth::invalidate($request->bearerToken());

            return response()->json([
                'success' => true,
                'message' => 'User logged out successfully'
            ]);
        }
        catch(JWTException $exception)
        {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, the user cannot be logged out'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }

    /**
     * get current logged in user
     */
    public function getAuthUser(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = JWTAuth::authenticate($request->bearerToken());

        return response()->json(['user' => $user]);
    }
}
