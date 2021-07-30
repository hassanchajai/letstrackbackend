<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderStatu extends Model
{
    use HasFactory;
    protected $table="order_status";
    public function orderjournal(){
        return $this->hasMany(OrderJournal::class);
    }
}
