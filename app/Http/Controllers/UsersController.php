<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
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
        $deliveries = Delivery::where("company_id", Auth::user()->company_id)->get();
        $deliveriesArr = array();
        //  if(count($deliveries)===0){
        //  return response()->json([
        //     "status"=>false,"message"=>"no Users yet"
        //  ],204);
        //  }
        foreach ($deliveries as $delivery) {
            $deliveriesArr[] = [
                "id" => $delivery->id,
                "name" => $delivery->name,
                "email" => $delivery->email,
                "phone" => $delivery->phone,
                "image" => $delivery->image,
                "created_at" => $delivery->created_at,
                "orders" => count($delivery->orders),
                "uid"=>$delivery->uid
            ];
        }
        return response()->json(["status" => true, "users" => $deliveriesArr], 200);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "uid" => "required|string|min:31|unique:deliveries",
            "email" => "required|email|unique:deliveries",
            "phone" => "required|string",
            "name" => "required|string|min:6",
        ]);
        if ($validator->fails()) {
            return response()->json([
                "errors" => $validator->errors()
            ]);
        }
        // store the image
        // dd($request->files);
        $image = "";
        // if ($request->hasFile("image")) {

            // $file=$request->file("image");
            // $filename=time().".".$file->getClientOriginalExtension();
            // $filepath="/uploads";
            // dd( $file->move($filepath,$filename));
            // Storage::move()
            // $image=$filepath.$filename;
        // }
        // create user
        $delivery =  Delivery::create([
            "uid" => $request->uid,
            "email" => $request->email,
            "phone" => $request->phone,
            "name" => $request->name,
            "company_id" => Auth::user()->company_id,
            "image" => $image
        ]);
        return response()->json([
            "message" => "User Created successfuly",
            "user" => $delivery
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $ordersProcessing = count(Order::join("order_status", "order_status.id", "=", "orders.order_statu_id")
            ->where("order_status.name", "=", "Processing")->where("delivery_id", $id)->get());
        $ordersCompleted = count(Order::join("order_status", "order_status.id", "=", "orders.order_statu_id")
            ->where("order_status.name", "=", "Completed")->where("delivery_id", $id)->get());
        $ordersCancelled = count(Order::join("order_status", "order_status.id", "=", "orders.order_statu_id")
            ->where("order_status.name", "=", "Cancelled")->where("delivery_id", $id)->get());
        $ordersEnDelivery = count(Order::join("order_status", "order_status.id", "=", "orders.order_statu_id")
            ->where("order_status.name", "=", "En Delivery")->where("delivery_id", $id)->get());

        $delivery = Delivery::find($id);
        $counter=$ordersProcessing+$ordersEnDelivery+$ordersCancelled+$ordersCompleted;
        return response()->json([
            "user" => $delivery,
            "ordersCounter"=>$counter,
            "orders" => [
                "ordersProcessing" => $ordersProcessing,
                "ordersCompleted" => $ordersCompleted,
                "ordersCancelled" => $ordersCancelled,
                "ordersEnDelivery" => $ordersEnDelivery
            ]
        ]);
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
        $validator = Validator::make($request->all(), [
            "name" => "required|min:6|string",
            "email" => "required|min:6|email",
            "phone" => "required|min:6|string",
        ]);
        if ($validator->fails()) {
            return response()->json([
                "errors" => $validator->errors()
            ]);
        }
        $delivery = Delivery::find($id);
        $delivery->update([
            "name"=>$request->input("name"),
            "email"=>$request->input("email"),
            "phone"=>$request->input("phone")
        ]);
        return response()->json(["message"=>"User updated succesfuly","user"=>$delivery], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Order::where("delivery_id",$id)->update(["delivery_id"=>0]);
        $delivery=Delivery::find($id);
        $delivery->delete();
        return response()->json([
            "message"=>"user deleted succesfuly"
        ]);
    }
    public function refresh(Request $request,$id)
    {
        $delivery = Delivery::find($id);
        $delivery->update([
            "uid"=>$request->input("uid")
        ]);
        return response()->json([
            "message"=>"user uid updated !",
            "user"=>$delivery
        ]);
    }
}
