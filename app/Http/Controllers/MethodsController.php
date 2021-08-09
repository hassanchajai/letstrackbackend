<?php

namespace App\Http\Controllers;

use App\Models\ApiMethod;
use App\Models\Application;
use App\Models\ApplicationMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MethodsController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth:company");
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $company_id = Auth::user()->company_id;
        $app = Application::where("company_id", $company_id)->get()->first();
        $methods = ApiMethod::all();
        $arr = [
            "name" => $app->name,
            
        ];
        foreach ($methods as $method) {
            $headers = $method->headers;
            // name ,path ,param
            $methodDetail=[
                "name"=>$method->name,
                "url"=>$method->path,
                "method"=>$method->type,
                "param"=>$method->param,
                "headers"=>[],
                "body"=>[]
            ];
            // headers
            $methodDetail["headers"][] = [
                "name" => $headers[0]->name,
                "defaultValue" => $app->key,
            ];
            $methodDetail["headers"][] = [
                "name" => $headers[1]->name,
                "defaultValue" => $app->host,
            ];
            // bodies
            foreach ($method->bodies as $body) {
                $methodDetail["body"][] = [
                    "name" => $body->name,
                    "defaultValue" => $body->defaultValue,
                ];
            }
            $arr["methods"][]=$methodDetail;
        }
        return response()->json($arr);
    }
}
