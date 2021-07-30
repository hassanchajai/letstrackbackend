<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;
    protected $fillable=[
        "name","email","uid","phone","image","company_id"
    ];
    public function orders(){
        return $this->hasMany(Order::class);
    }
    public function company(){
        return $this->belongsTo(Company::class);
    }
}
