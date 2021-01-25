<?php

namespace App\Repositories;

use App\Order;

class OrderRepository
{
    private $model;

    public function __construct()
    {
        $this->model = new Order();
    }
    
    public function all()
    {
        return $this->model->with(['gateway'])->get();
    }

    public function get($id)
    {
        return $this->model->find($id);
    }

    public function getWhere($field, $value)
    {
        return $this->model->where($field,$value)->with(['gateway'])->first();
    }

    public function save(Order $order)
    {
        $order->save();
        return $order;
    }

    public function delete(Order $order)
    {
        if($order->gateway->count() > 0)
        {
            $order->gateway->delete();
        }

        $order->delete();

        return $order;
    }
}