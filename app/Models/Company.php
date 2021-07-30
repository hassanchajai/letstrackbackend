<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;
    protected $fillable=[
        "name","logo","Email","pack_id"
    ];
    public function orders(){
        return $this->hasMany(Order::class);
    }
    public function users(){
        return $this->hasMany(CompanyUser::class);
    }
}
