<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware("auth:company");
    }
    public function index()
    {
        $company_id=Auth::user()->company_id;
        // created_at , location , spam count order id order status
        $orders=Order::where("company_id",$company_id)->orderBy("created_at","desc")->get();
        $arr=[];
        foreach ($orders as $key => $item) {
            $arr[]=[
                "order_id"=>$item->id,
                "pickup"=>$item->created_at,
                "location"=>$item->shipping_address,
                "phone"=>$item->customer->phone,
                "spams"=>count($item->customer->spams),
                "status"=>$item->status->name
            ];
        }
        return response()->json([
            "orders"=>$arr,
            "count"=>count($orders)
        ],200);
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
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $company_id=Auth::user()->company_id;
        $order=Order::where("id",$id)->where("company_id",$company_id)->first();
        $item=[
            "order_date"=>$order->order_date,
            "created_at"=>$order->created_at,
            "delivery"=>$order->delivery,
            "phone"=>$order->customer->phone,
            "order_detail"=>$order->orderdetail,
            "location"=>$order->shipping_address
        ];
        return response()->json(["order"=>$item]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
