<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthCompanyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:company', ['except' => ['login', 'register']]);

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

        if (!$token = $this->guard()->attempt([
            "email" => $req->email, "password" => $req->password
        ])) {
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
            "email" => "required|email|unique:company_users",
            "firstname" => "required|min:6|max:101",
            "lastname" => "required|min:6|max:101",
            "password" => "required|min:6",
            // "company_users_role_id" => "required|integer",
           
        ]);
        if ($validator->fails()) {
            return response()->json([
                $validator->errors()
            ], 422);
        }
        $company= Company::create(
            [
                "Email"=>$req->email,
                "name"=>"name",
                "logo"=>$req->logo,
                "pack_id"=>$req->pack_id
            ]
        );
        $user = CompanyUser::create(
            [
                "email" => $req->email,
                "firstname" => $req->firstname,
                "lastname" => $req->lastname,
                "password" => bcrypt($req->password),
                "company_id" => $company->id
            ]
        );
        return response()->json([
            "message" => "Created Succefuly!",
            "user" => $user
        ], 200);
    }
    public function signout()
    {
        $this->guard()->logout();

        return response()->json(['message' => 'Successfully logged out']);
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
        return Auth::guard("company");
    }
    // public function nosign()
    // {
    //     return response()->json([
    //         "status" => false,
    //         "message" => "No Authorized"
    //     ], 403);
    // }
}
