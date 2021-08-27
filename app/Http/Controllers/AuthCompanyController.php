<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyUser;
use App\Models\Application;
use App\Models\CompanyDetail;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
            // "host"=>"required|url"
            // "company_users_role_id" => "required|integer",

        ]);
        if ($validator->fails()) {
            return response()->json([
                "errors" =>  $validator->errors()
            ]);
        }
        $company = Company::create(
            [
                "Email" => $req->email,
                "name" => "name",
                "logo" => $req->logo,
                "pack_id" => $req->pack_id
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
        $companyDetail = CompanyDetail::create(
            [
                "name" => "",
                "site" => $req->site,
                "address" => $req->city,
                "country" => $req->country,
                "company_id" => $company->id
            ]
        );
        $count = count(Application::where("company_id", $company->id)->get()) + 1;
        $key = $this->uniqidReal(35);
        $nameApp = $company->name . substr(time(), 0, 5) . " Application n°" . $count . "  " . "@" . date("Y");
        $app = Application::create([
            "name" => $nameApp,
            "host" => $req->site,
            "key" => $key,
            "company_id" => $company->id
        ]);
        return response()->json([
            "message" => "Created Succefuly!",
            "user" => $user,
            "app" => $app
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
    public function updateCompanyDetail(Request $req)
    {
        $validator = Validator::make($req->all(), [
            "nameCompany" => "required",
            "Site" => "required|min:6|max:101",
            "City" => "required|min:6|max:101",
            "Country" => "required",
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "errors" =>  $validator->errors()
            ]);
        }
        $user = $this->guard()->user();
        $company = CompanyDetail::where("company_id", $user->company_id);
        $company->update([
            "name" => $req->nameCompany, "site" => $req->Site, "address" => $req->City, "country" => $req->Country
        ]);
        return response()->json([
            "status" => true,
            "message" => "Company Detail updated !"
        ]);
    }
    public function updateUserDetail(Request $req)
    {
        $validator = Validator::make($req->all(), [
            "email" => "required|email",
            "firstname" => "required|min:6|max:101",
            "lastname" => "required|min:6|max:101",
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "errors" =>  $validator->errors()
            ]);
        }
        $user = $this->guard()->user();
        $company = CompanyUser::find($user->id);
        $company->update([
            "email" => $req->email,
            "firstname" => $req->firstname,
            "lastname" => $req->lastname,
        ]);
        return response()->json([
            "status" => true,
            "message" => "Company User updated !"
        ]);
    }
    public function pasword(Request $req)
    {
        $validator = Validator::make($req->all(), [

            "newpassword" => "required|min:6|max:101",
            "password" => "required",
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "errors" =>  $validator->errors()
            ]);
        }
        $user = $this->guard()->user();
        $company = CompanyUser::find($user->id);
        if (!Hash::check($req->password, $user->password)) {
            return response()->json([
                "status" => true,
                "message" => "password Incorrect"
            ]);
        }

        $company->update([
            "password" =>   Hash::make($req->newpassword)

        ]);
        return response()->json([
            "status" => true,
            "message" => " User password updated !"
        ]);
    }
    public function profile()
    {
        $user = $this->guard()->user();
        $company = CompanyDetail::where("company_id",$user->company_id)->first();
        return response()->json([
            "user" => $user,
            "company" => $company
        ]);
    }
    protected function guard()
    {
        return Auth::guard("company");
    }
}
