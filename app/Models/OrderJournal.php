<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderJournal extends Model
{
    use HasFactory;
    protected $table="order_journals";
    protected $fillable=[
        "order_statu_id",
        "order_id",
        "message"
    ];
    public function orderstatu(){
        return $this->belongsTo(OrderStatu::class);
    }
    public function order(){
        return $this->belongsTo(Order::class);
    }
}
