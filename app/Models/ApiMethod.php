<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiMethod extends Model
{
    use HasFactory;
    public function bodies(){
        return $this->hasMany(ApiMethodBody::class,"api_method_id");
    }
    public function headers(){
        return $this->hasMany(ApiMethodHeader::class);
    }
}
