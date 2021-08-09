<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Order extends Model
{
    use HasFactory;
    // ,`shipping_address`,`order_date`,`company_id`,`customer_id`,`order_statu_id`,`delivery_id`
    protected $fillable=[
        "order_uid","client_api","shipping_address","order_date","company_id","customer_id","order_statu_id","delivery_id"
    ];
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }
    public function orderdetail()
    {
        return $this->hasMany(OrderDetail::class);
    }
    public function orderjournals()
    {
        return $this->hasMany(OrderJournal::class);
    }
    public function status(){
        return $this->belongsTo(OrderStatu::class,"order_statu_id");
    }
}
