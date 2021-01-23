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

    public function get_payment_result($status)
    {
        factory(Product::class,1)->create();
        $product = Product::first();

        factory(Order::class)->create([
            'product_id' => $product->id,
        ]);
        
        $newOrder = $product->getOrder();

        $status = [
            'status'  => $status,
            'reason'  => 'XX',
            'message' => 'The message of the status',
            'date'    => '23-01-2021',
        ];

        $response = $this->post('payment-result',
            [
                'reference' => $newOrder->number,
                'status'    => $status
            ]
        );

        $response->assertStatus(200);

        $response->assertViewIs('payments.show');
        $response->assertViewHas([
            'order'=> $newOrder,
            'status' => $status['status']
        ]);
    }

    /**
     * A buyer can see the REJECTED payment result.
     * @test
     * @return void
     */
    public function a_buyer_can_see_payment_rejected_result()
    {
        $this->withoutExceptionHandling();

        $this->get_payment_result('REJECTED');

    }

    /**
     * A buyer can see the pending payment result.
     * @test
     * @return void
     */
    public function a_buyer_can_see_payment_pending_result()
    {
        $this->withoutExceptionHandling();

        $this->get_payment_result('PENDING');
          
    }

    /**
     * A buyer can see the pending payment result.
     * @test
     * @return void
     */
    public function a_buyer_can_see_payment_approved_result()
    {
        $this->withoutExceptionHandling();

        $this->get_payment_result('APPROVED');

        
    }

    /**
     * Validate data before to show a payment result.
     * @test
     * @return void
     */
    public function payment_result_must_validate_data()
    {
        $this->withoutExceptionHandling();

        $response = $this->post('payment-result',
            [
                'reference' => "dsdfsdfddd",
                'status'    => "FAKER"
            ]
        );

        $response->assertSessionHasErrors([
            'reference',
            'status',
        ]);

        
    }

    
}
