<?php

namespace App;

use App\Order;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'price','currency','description',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function getOrder($id)
    {
        return $this->orders->where('product_id',$id)->first();
    }
            
}
