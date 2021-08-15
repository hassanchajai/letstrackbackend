<?php

namespace App\Http\Controllers;

use App\Models\CompanyDetail;
use App\Models\Order;
use App\Models\OrderJournal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserInterfaceController extends Controller
{
    public function search(Request $req){
        $validator = Validator::make($req->all(),
        [
            "number"=>"required|min:6"
        ]);
        if($validator->fails()){
            return response()->json([
                "status"=>false,
                "message"=>"Number must be valid"
            ]);
        }
        $order=Order::where("order_uid",$req->number)->first();
        if(!$order){
            return response()->json([
                "status"=>false,
                "message"=>"Order not exist"
            ]);
        }
        $company_informations=CompanyDetail::where("company_id",$order->company_id)->first();
        $order_journals=OrderJournal::where("order_id",$order->id)->orderBy("id","desc")->get();
        $orderjournal=[];
        foreach ($order_journals as $key => $value) {
           $orderjournal[]=[
               "id"=>$value->id,
               "message"=>$value->message,
               "statu"=>$value->orderstatu->name,
               "created_at"=>$value->created_at
           ];
        }
        return response()->json([
            "status"=>true,
            "message"=>"Order exist",
            "company"=>$company_informations,
            "order"=>$order,
            "order_journal"=>$orderjournal
        ]);

    }
}
