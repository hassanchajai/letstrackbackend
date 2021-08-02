<?php

namespace App\Http\Controllers;

use App\Models\Spam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SpamController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth:company");
    }
    public function spam(Request $request)
    {
        if ($request->type === "add") {
            $spam = new Spam();
            $spam->customer_id = $request->customer_id;
            $spam->user_id = Auth::user()->company_id;
            $spam->order_id = $request->order_id;
            $spam->save();
            return response()->json([
                "message" => "Spam added succefuly"
            ]);
        }
        $spam =Spam::find($request->spam_id);
        $spam->delete();
        return response()->json([
            "message" => "Spam deletd succefuly"
        ]);
    }
}
