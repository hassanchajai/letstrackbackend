<?php

namespace App\Http\Controllers;

use App\Models\OrderJournal;
use Illuminate\Http\Request;

class OrderJournalsController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth:company");
    }
    public function show(Request $request,$id){
        $ordersJouranl=OrderJournal::where("order_id",$id)->get();
        return response()->json([
            "order_journal"=>$ordersJouranl
        ]);
    }
    public function destroy(Request $request,$id){
        $ordersJouranl=OrderJournal::find($id);
        $ordersJouranl->delete();
        return response()->json([
            "message"=>"order journal Item Delted succesfuly"
        ]);
    }
}
