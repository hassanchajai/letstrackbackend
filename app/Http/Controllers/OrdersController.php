<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderJournal;
use App\Models\Spam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware("auth:company",["except"=>["store"]]);
    }
    public function index(Request $request)
    {
        $company_id=Auth::user()->company_id;
        $orders=[];

       $orders =  Order::where("company_id",$company_id);
       if(isset($request->status) && $request->status != "All")  $orders=$orders->where("order_statu_id",$request->status);
       if(isset($request->phone) && !empty($request->phone))  $orders=$orders->join("customers","customers.id","=","orders.customer_id")->where("customers.phone","like",$request->phone."%");
       if(isset($request->delivery_id) && !empty($request->delivery_id) && $request->delivery_id != "All")  $orders=$orders->where("delivery_id",$request->delivery_id);
      
       $orders=$orders->orderBy("orders.created_at","desc")->get();
    //   dd($orders); 
       $arr=[];
        foreach ($orders as $key => $item) {

            $arr[]=[
                "order_id"=>$item->id,
                "pickup"=>$item->order_date,
                "location"=>$item->shipping_address,
                "phone"=>$item->customer->phone,
                "spams"=>count($item->customer->spams),
                "status"=>$item->status->name,
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


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // API for consome in comapny site
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
        $idUser=Auth::user()->id;
        $spamchecked=Spam::where("user_id",$idUser)->get();
        $order=Order::where("id",$id)->where("company_id",$company_id)->first();
        $orderjournal=[];
        foreach ($order->orderjournals as $key => $value) {
           $orderjournal[]=[
               "id"=>$value->id,
               "message"=>$value->message,
               "statu"=>$value->orderstatu->name,
               "created_at"=>$value->created_at
           ];
        }
        $item=[
            "order_id"=>$order->id,
            "order_date"=>$order->order_date,
            "created_at"=>$order->created_at,
            "delivery"=>$order->delivery,
            "customer"=>$order->customer,
            "order_detail"=>$order->orderdetail,
            "location"=>$order->shipping_address,
            "status"=>$order->status,
            "spamChecked"=>count($spamchecked),
            "spam"=>$spamchecked,
            "order_journal"=>$orderjournal
        ];
        return response()->json(["order"=>$item]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        $validate=Validator::make($request->all(),[
            "date"=>"required",
            "delivery_id"=>"required"
        ]);
        if($validate->fails()){
            return response()->json([
                "errors"=>$validate->errors()
            ],422);
        }
        $order=Order::find($id);
        $order->order_date=$request->date;
        $order->delivery_id=$request->delivery_id;
        $order->save();
        return response()->json([
            "message"=>"Order updated succesufuly",
            "date"=>$request->date
        ],200);
    }
    public function updateAddress(Request $request,$id)
    {
        $validate=Validator::make($request->all(),[
            "shipping_address"=>"required"
        ]);
        if($validate->fails()){
            return response()->json([
                "errors"=>$validate->errors()
            ],422);
        }
        $order=Order::find($id);
        $order->shipping_address=$request->shipping_address;
        $order->save();
        return response()->json([
            "message"=>"Order Date updated succesufuly"
        ],200);
    }

    public function updateStatus(Request $request, $id){
        $validate=Validator::make($request->all(),[
            "status_id"=>"required"
        ]);
        if($validate->fails()){
            return response()->json([
                "errors"=>$validate->errors()
            ],422);
        }
        $order=Order::find($id);
        $order->order_statu_id=$request->status_id;
        // $order->message=$request->message;
        $order->save();
        OrderJournal::create([
            "order_statu_id"=>$request->status_id,
            "order_id"=>$id,
            "message"=>$request->message
        ]);
        return response()->json([
            "message"=>"Order Status updated succesufuly"
        ],200);
    }

    // public function spam(Request $request, $id){
    //     $validate=Validator::make($request->all(),[
    //         "status_id"=>"required|number"
    //     ]);
    //     if($validate->fails()){
    //         return response()->json([
    //             "errors"=>$validate->errors()
    //         ],422);
    //     }
    //     $order=Order::find($id);
    //     $order->status_id=$request->status_id;
    //     $order->save();
    //     return response()->json([
    //         "message"=>"Order Status updated succesufuly"
    //     ],200);
    // }

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
