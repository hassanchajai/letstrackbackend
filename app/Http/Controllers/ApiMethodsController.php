<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetail;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiMethodsController extends Controller
{
    private function uniqidReal($lenght = 7)
    {

        if (function_exists("random_bytes")) {
            $bytes = random_bytes(ceil($lenght / 2));
        } elseif (function_exists("openssl_random_pseudo_bytes")) {
            $bytes = openssl_random_pseudo_bytes(ceil($lenght / 2));
        } else {
            throw new Exception("no cryptographically secure random function available");
        }
        return substr(bin2hex($bytes), 0, $lenght);
    }
    public function add(Request $req)
    {
        // $ipAddress = $request->ip();


        $apikey = $req->header("api_key", false);
        $apiHost = $req->header("api_host", false);
        if ($apikey && $apiHost) {
            // get application detail
            $appCompany = Application::where("key", $apikey)->where("host", $apiHost)->first();
            if ($appCompany) {
                $validator = Validator::make($req->all(), [
                    "product_name" => "required|string",
                    "price" => "required",
                    "qte" => "required",
                    "shipping_address" => "required",
                    "phone" => "required|string|min:6|max:15"
                ]);
                if ($validator->fails()) {
                    return response()->json([
                        "message" => "bad request",
                        "errors" => $validator->errors()
                    ], 429);
                }
                // part customer 
                $idCustomer = 0;
                $customer = Customer::where("phone", $req->phone)->first();
                if ($customer) $idCustomer = $customer->id;
                else $idCustomer = Customer::create(["phone" => $req->phone])->id;
                // end of part customer
                // part order 
                $idCompany = $appCompany->company_id;
                // `order_uid`,`client_api`,`shipping_address`,`order_date`,`company_id`,`customer_id`,`order_statu_id`,`delivery_id`
                $order = Order::create([
                    "order_uid" => $this->uniqidReal(10),
                    "client_api" => $req->ip(),
                    "shipping_address" => $req->shipping_address,
                    "order_date" => date("Y-m-d h:i:s"),
                    "company_id" => $idCompany,
                    "customer_id" => $idCustomer,
                    "order_statu_id" => 1,
                    "delivery_id" => 0
                ]);
                $orderDetail = OrderDetail::create([
                    "product" => $req->product_name,
                    "price" => $req->price,
                    "qte" => $req->qte,
                    "order_id" => $order->id
                ]);
                // end of detail order
                return response()->json([
                    "TrackingNumber"=>$order->order_uid
                ]);
            } else {
                return response()->json([
                    "message" => "this application does not exist !"
                ]);
            }
        } else if ($apikey) {
            return response()->json([
                "message" => "Api host must be required"
            ]);
        } else {
            return response()->json([
                "message" => "Api key must be required"
            ]);
        }
    }
}
