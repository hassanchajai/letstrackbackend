<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Order extends Model
{
    use HasFactory;
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
        return $this->hasMany(Order::class);
    }
    public function status(){
        return $this->belongsTo(OrderStatu::class,"order_statu_id");
    }

    // public function getCurrentCompany()
    // {
    //     $company_id = Auth::user()->company_id;

    //     return $this->where('company_id', $company_id);
    // }
}
