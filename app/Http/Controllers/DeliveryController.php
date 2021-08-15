<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Order;
use App\Models\OrderJournal;
use App\Models\OrderStatu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DeliveryController extends Controller
{
    public function orders(Request $req)
    {
        $uid = $req->header("uid", false);
        if ($uid) {
            $Delievery = Delivery::where("uid", $uid)->first();
            if ($Delievery) {
                $ProcessId=OrderStatu::where("name","Processing")->first();
                $orders = Order::where("delivery_id", $Delievery->id)->where("order_statu_id","<>",$ProcessId->id)->orderBy("updated_at","desc")->get();
                // Processing
                $arr = [];
                foreach ($orders as $key => $item) {

                    $arr[] = [
                        "order_id" => $item->id,
                        "pickup" => $item->order_date,
                        "location" => $item->shipping_address,
                        "phone" => $item->customer->phone,
                        "spams" => count($item->customer->spams),
                        "status" => $item->status->name,
                    ];
                }
                return response()->json([
                    "message" => "User Found !",
                    "orders" => $arr
                ]);
            } else {
                return response()->json([
                    "message" => "User Not found !"
                ]);
            }
        } else {
            return response()->json([
                "message" => "UID header must be required !"
            ]);
        }
    }
    public function show(Request $req,$id){
        $uid = $req->header("uid", false);
        if ($uid) {
            $Delievery = Delivery::where("uid", $uid)->first();
            if ($Delievery) {
                $order=Order::where("id",$id)->where("delivery_id",$Delievery->id)->first();
                $orderJournals=OrderJournal::where("order_id",$order->id)->orderBy("id","desc")->get();
                $orderjournal=[];
                foreach ($orderJournals as $key => $value) {
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
                    "order_journal"=>$orderjournal,
                    "ip"=>$order->client_api,
                    "number"=>$order->order_uid
                ];
                return response()->json(["order"=>$item]);
            }
        }
    }
    public function status(Request $req){
        $ProcessId=OrderStatu::where("name","Processing")->first();
        $orderstatus=OrderStatu::where("id","<>",$ProcessId->id)->get();
        return response()->json(["status"=>$orderstatus],200);
    }
    public function updateStatus(Request $request,$id){
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
}
