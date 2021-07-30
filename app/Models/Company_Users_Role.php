<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company_Users_Role extends Model
{
    use HasFactory;
    public function users(){
        return $this->hasMany(CompanyUser::class);
    }
}
