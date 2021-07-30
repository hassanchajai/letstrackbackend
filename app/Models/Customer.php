<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    public function spams(){
        return $this->hasMany(Spam::class);
    }
    public function orders(){
        return $this->hasMany(Order::class);
    }
}
