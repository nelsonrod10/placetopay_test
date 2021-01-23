<?php

namespace Tests\Feature;

use App\Order;
use App\Product;
use Tests\TestCase;
use Illuminate\Http\Request;
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

        $response->assertStatus(302);

        $newOrder = $product->getOrder();
        
        $redirectResponse = $this->get('validate-payment/'.$newOrder->number);

        $status = [
            'status'  => 'APPROVED',
            'reason'  => 'XS',
            'message' => 'The message of the status',
            'date'    => '23-01-2021',
        ];
            
        $redirectResponse->assertRedirect(route('payment-result', 
            new Request([
                'reference' => $newOrder->number,
                'status'    => $status
            ])
        ));
    }
    
    /**
     * A buyer can see the approved payment result.
     * @test
     * @return void
     */
    public function a_buyer_can_see_payment_approved_result()
    {
        $this->withoutExceptionHandling();

        factory(Product::class,1)->create();
        $product = Product::first();

        factory(Order::class)->create([
            'product_id'          => $product->id,
        ]);
        
        $newOrder = $product->getOrder();

        $status = [
            'status'  => 'APPROVED',
            'reason'  => 'XX',
            'message' => 'The message of the status',
            'date'    => '23-01-2021',
        ];

        $redirectResponse = $this->post('payment-result',
            [
                'reference' => $newOrder->number,
                'status'    => $status
            ]
        );

        $redirectResponse->assertStatus(200);

        $redirectResponse->assertViewIs('payments.show');
        $redirectResponse->assertViewHas([
            'order'=> $newOrder,
            'status' => $status['status']
        ]);
    }

    
}
