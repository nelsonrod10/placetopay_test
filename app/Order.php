<?php

namespace App;

use App\PaymentGateway;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id','number','customer_name', 'customer_email', 'customer_mobile', 'status',
    ];

    public function gateway()
    {
        return $this->hasOne(PaymentGateway::class);
    }
}
