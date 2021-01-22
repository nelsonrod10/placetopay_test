<?php

namespace App;

use App\Order;
use Illuminate\Database\Eloquent\Model;

class PaymentGateway extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'enterprise',
        'payment_data',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class,'order_id');
    }

}
