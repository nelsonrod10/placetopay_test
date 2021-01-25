<?php

namespace App\Repositories;

use App\PaymentGateway;

class PaymentGatewayRepository
{
    private $model;

    public function __construct()
    {
        $this->model = new PaymentGateway();
    }
    
    public function all()
    {
        return $this->model->all();
    }

    public function get($id)
    {
        return $this->model->find($id);
    }

    public function save(PaymentGateway $payment)
    {
        $payment->save();
        return $payment;
    }

    public function delete(PaymentGateway $payment)
    {
        
        $payment->delete();

        return $payment;
    }
}