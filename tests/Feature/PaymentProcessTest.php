<?php

namespace Tests\Feature;

use App\Order;
use App\Product;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PaymentProcessTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Verify the state of the payment.
     * @test
     * @return void
     */
    public function a_buyer_is_redirect_to_verify_payment_state()
    {
        $this->withoutExceptionHandling();

        factory(Product::class,1)->create();

        $product = Product::first();

        $data = [
            'product_id'          => $product->id,
            'customer_name'    => 'Nelson Rodriguez',
            'customer_email'   => 'bejin3@hotmail.com',
            'customer_mobile'  => '3167585671'  
        ];

        $response = $this->post('orders',$data);

        $newOrder = $product->getOrder($product->id);
        
        $placeToPay = $newOrder->gateway()->first();

        $dataGateway =  json_decode($placeToPay->payment_data,true);

        $redirectResponse = $this->get('payment-result/'.$dataGateway['request_id']);

        $redirectResponse->assertStatus(200);
    }    

    
}
