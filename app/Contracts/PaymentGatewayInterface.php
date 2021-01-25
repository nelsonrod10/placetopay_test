<?php

namespace App\Contracts;

use App\Product;

interface PaymentGatewayInterface
{
    /**
     * Make a request to payment gateway
     *
     * @param  mixed  $buyerData, $product
     * @return string
     */   
    public function makeRequest(Array $buyerData=null, Product $product);

    /**
     * Consult the payment result
     * 
     * @param  string  $id
     * @return string
     */
    public function paymentResult($id);
}
