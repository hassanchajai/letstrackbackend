<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Order;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    public function orders(Request $req)
    {
        $uid = $req->header("uid", false);
        if ($uid) {
            $Delievery = Delivery::where("uid", $uid)->first();
            if ($Delievery) {

                $orders = Order::where("delivery_id", $Delievery->id)->get();
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
}
