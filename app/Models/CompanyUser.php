<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;

class CompanyUser extends Authenticatable implements JWTSubject 
{
    use HasFactory;
    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'password',
        // "company_users_role_id",
        "company_id"
    ];
    protected $hidden=[
        "password"
    ];
    
    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
    public function journals(){
        return $this->hasMany(Company_users_journal::class);
    }
    public function company(){
        return $this->belongsTo(Company::class);
    }
    public function spams(){
        return $this->hasMany(Spam::class);
    }
}
