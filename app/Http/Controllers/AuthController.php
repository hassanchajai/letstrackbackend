<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware("auth:api", [
            "except" => ["login", "register"]
        ]);
        // $this->middleware('auth:api', ['except' => ['login', 'register']]);

    }
    public function login(Request $req)
    {
        // return response()->json($this->guard()->user());
        $validator = Validator::make(
            $req->all(),
            [
                'email'    => 'required|email',
                'password' => 'required|string|min:6',
            ]
        );

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $token_validity = (24 * 60);
        // return response()json($this->guard()->factory());
        $this->guard()->factory()->setTTL($token_validity);

        if (!$token = $this->guard()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }
    protected function respondWithToken($token)
    {
        return response()->json(
            [
                "token" => $token,
                "type" => "bearer",
                "expire_date" => $this->guard()->factory()->getTTL(),

            ]
        );
    }
    public function register(Request $req)
    {
        $validator = Validator::make($req->all(), [
            "email"=>"required|email|unique:users",
            "name"=>"required|min:6|max:101",
            "password"=>"required|min:6"
        ]);
        if ($validator->fails()) {
            return response()->json([
                $validator->errors()
            ], 422);
        }
       $user= User::create(
            array_merge($validator->validated()),
            ["password" => bcrypt($req->password)]
        );
        return response()->json([
            "message"=>"user Created Succefuly!",
            "user"=>$user
        ],200);
    }
    public function signout()
    {
        return $this->guard()->logout();
    }
    public function refresh()
    {
        return $this->respondWithToken($this->guard()->refresh());
    }
    public function profile()
    {
        return response()->json($this->guard()->user());
    }
    protected function guard()
    {
        return Auth::guard();
    }
    public function nosign(){
        return response()->json([
            "status"=>false,
            "message"=>"No Authorized"
        ],403);
    }
}
