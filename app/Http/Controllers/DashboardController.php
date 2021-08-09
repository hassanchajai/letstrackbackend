<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderJournal;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth:company");
    }
    public function orders()
    {
        $company_id = Auth::user()->company_id;

        // dates
        $beginlastmonth =  date("Y-m-d H:i:s", mktime(0, 0, 0, date("m") - 1, 1,   date("Y")));
        $endlastmonth =  date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), 1,   date("Y")));
        // get all orders
        $ordersProcessing = count(Order::join("order_status", "order_status.id", "=", "orders.order_statu_id")
            ->where("order_status.name", "=", "Processing")->where("company_id", $company_id)->get());
        $ordersCompleted = count(Order::join("order_status", "order_status.id", "=", "orders.order_statu_id")
            ->where("order_status.name", "=", "Completed")->where("company_id", $company_id)->get());
        $ordersCancelled = count(Order::join("order_status", "order_status.id", "=", "orders.order_statu_id")
            ->where("order_status.name", "=", "Cancelled")->where("company_id", $company_id)->get());
        $ordersEnDelivery = count(Order::join("order_status", "order_status.id", "=", "orders.order_statu_id")
            ->where("order_status.name", "=", "En Delivery")->where("company_id", $company_id)->get());
        // get the orders of last month
        $ordersProcessingLastMonth = count(Order::join("order_status", "order_status.id", "=", "orders.order_statu_id")
            ->where("order_status.name", "=", "Processing")
            ->whereBetween("orders.order_date", [$beginlastmonth, $endlastmonth])->where("company_id", $company_id)->get());

        $ordersCompletedLastMonth = count(Order::join("order_status", "order_status.id", "=", "orders.order_statu_id")
            ->where("order_status.name", "=", "Completed")
            ->whereBetween("orders.order_date", [$beginlastmonth, $endlastmonth])->where("company_id", $company_id)->get());

        $ordersCancelledLastMonth = count(Order::join("order_status", "order_status.id", "=", "orders.order_statu_id")
            ->where("order_status.name", "=", "Cancelled")
            ->whereBetween("order_date", [$beginlastmonth, $endlastmonth])->where("company_id", $company_id)->get());
        $ordersEnDeliveryLastMonth = count(Order::join("order_status", "order_status.id", "=", "orders.order_statu_id")
            ->where("order_status.name", "=", "En Delivery")
            ->whereBetween("orders.order_date", [$beginlastmonth, $endlastmonth])->where("company_id", $company_id)->get());
        //  get the orders of current month
        $ordersProcessingCurrentMonth = count(Order::join("order_status", "order_status.id", "=", "orders.order_statu_id")
            ->where("order_status.name", "=", "Processing")
            ->where("orders.order_date", ">", $endlastmonth)
            ->where("company_id", $company_id)->get());
        $ordersCompletedCurrentMonth = count(Order::join("order_status", "order_status.id", "=", "orders.order_statu_id")
            ->where("order_status.name", "=", "Completed")
            ->where("orders.order_date", ">=", $endlastmonth)
            ->where("company_id", $company_id)->get());
        $ordersCancelledCurrentMonth = count(Order::join("order_status", "order_status.id", "=", "orders.order_statu_id")
            ->where("order_status.name", "=", "Cancelled")
            ->where("orders.order_date", ">=", $endlastmonth)
            ->where("company_id", $company_id)->get());
        $ordersEnDeliveryCurrentMonth = count(Order::join("order_status", "order_status.id", "=", "orders.order_statu_id")
            ->where("order_status.name", "=", "En Delivery")
            ->where("order_date", ">=", $endlastmonth)
            ->where("company_id", $company_id)->get());
            // model::join()
        // end 
        // calculate the percent
        $percentProcessing = 0;
        $percentCompleted = 0;
        $percentCancelled = 0;
        $percentEnDelivery = 0;
        if ($ordersProcessingLastMonth !== 0) $percentProcessing = (($ordersProcessingCurrentMonth - $ordersProcessingLastMonth) / $ordersProcessingLastMonth) * 100;
        if ($ordersCompletedLastMonth !== 0) $percentCompleted = (($ordersCompletedCurrentMonth - $ordersCompletedLastMonth) / $ordersCompletedLastMonth) * 100;
        if ($ordersCancelledLastMonth !== 0) $percentCancelled = (($ordersCancelledCurrentMonth - $ordersCancelledLastMonth) / $ordersCancelledLastMonth) * 100;
        if ($ordersEnDeliveryLastMonth !== 0) $percentEnDelivery = (($ordersEnDeliveryCurrentMonth - $ordersEnDeliveryLastMonth) / $ordersEnDeliveryLastMonth) * 100;
        //  Order::whereBetween("created_at",[$beginlastmonth,$endlastmonth])->get();
        return response()->json([
            "orders" => [
                "ordersProcessing" => $ordersProcessing,
                "ordersCompleted" => $ordersCompleted,
                "ordersCancelled" => $ordersCancelled,
                "ordersEnDelivery" => $ordersEnDelivery
            ],
            "ordersPercent" => [
                "ordersProcessing" => $percentProcessing,
                "ordersCompleted" => $percentCompleted,
                "ordersCancelled" => $percentCancelled,
                "ordersEnDelivery" => $percentEnDelivery
            ]
        ]);
    }
    public function lastorders()
    {
        $company_id = Auth::user()->company_id;
        $orders = Order::where("company_id", $company_id)->orderBy("order_date", "desc")->take(6)->get();
        $ordersArr = [];
        foreach ($orders as $order) {
            $ordersArr[] = [
                "id" => $order->id,
                "shipping_address" => $order->shipping_address,
                "created_at" => $order->order_date
            ];
        }
        return response()->json([
            "orders" => $ordersArr
        ]);
    }
    public function chartdata(){
        $company_id = Auth::user()->company_id;
        $result = Order::selectRaw(' monthname(order_date) as month, count(*) count')
            ->where("company_id", $company_id)
                ->groupBy('month')
                ->orderBy("order_date","desc")
                ->take(6)
                ->get();
        return response()->json(["orders"=>$result]);
    }
}
