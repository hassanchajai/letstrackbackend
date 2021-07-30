<?php

namespace App\Http\Controllers;

use App\Models\OrderStatu;
use Illuminate\Http\Request;

class OrderStatusController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth:company");
    }
    public function index(){
        $status=OrderStatu::all();
        return response()->json([
            "status"=>$status
        ]);
    }
}
