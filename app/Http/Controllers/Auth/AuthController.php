<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\{LoginRequest, RegisterRequest};
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\JWTAuth;

class AuthController extends Controller
{
    protected $auth;

    /*
    |--------------------------------------------------------------------------------|
    | Auth Controller
    |--------------------------------------------------------------------------------
    | This controller is responsible for handling register , login , forget password
    | -------------------------------------------------------------------------------
    */
    /**
     *  construct pram instance Of JwtAuthClass
     */
    public function __construct(JWTAuth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * @param RegisterRequest $request
     *  1- validate request in RegisterRequest
     *  2- if validation true then create user
     *     else response validation error will back
     * 3- attempt auth
     * 4- @return user with jwt token
     */
    public function register(RegisterRequest $request)
    {
        $user = $this->createUser($request);
        if (!$user) {
            return $this->responseError(['database error'], 'filed in registrations', 500);
        }
        $token = $this->attemptUser($request->only('email', 'password'));
        return $this->userResponse($user, $token, 200);
    }

    public function login(LoginRequest $request)
    {
        $token = $this->attemptUser($request->only('email', 'password'));
        $user = User::where(['email' => $request->email])->first();
        return $this->userResponse($user, $token, 200);
    }

    /**
     * @param $request
     * @return user == false in case of creation filed  otherwise return user
     */
    public function createUser($request)
    {
        DB::beginTransaction();

        try {
            $user = User::create([
                'email' => $request->email,
                'name' => $request->name,
                'password' => Hash::make($request->password),
            ]);
            DB::commit();
            return $user;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    /**
     * @param array $credentials
     * @return string $token form user
     */
    public function attemptUser(array $credentials)
    {
        try {
            if (!$token = $this->auth->attempt($credentials)) {
                return $this->responseError('can not login with this credentials', ['can not login with this credentials'], 401);
            }
        } catch (JWTException $exception) {
            return $this->responseError('can not login with this credentials', ['can not login with this credentials'], 401);

        }
        return $token;
    }

    /**
     * @return object json
     * 1- first get token from auth user
     * 2- invalidate this token for this user
     * 3- return json with data null with status code 200
     */
    public function logout()
    {
        $this->auth->invalidate($this->auth->getToken());
        return response(['data' => null], 200);
    }

    /**
     * @return object user
     * 1- run under middleware jwt.auth
     * 1- get user with token from request
     */
    public function user(Request $request)
    {
        return response()->json([
            'data' => $request->user(),

        ]);
    }
}
