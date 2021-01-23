<?php

namespace Tests\Feature;

use App\Order;
use App\Product;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderProductTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Select a product for purchase.
     * @test
     * @return void
     */
    public function a_product_can_be_selected_for_purchase()
    {
        $this->withoutExceptionHandling();

        factory(Product::class,1)->create();

        $product = Product::first();

        $response = $this->get('create-order/'.$product->id);
        
        $response->assertStatus(200);

        $response->assertViewIs('orders.create');

        $response->assertViewHas('product',$product);
    }

    /**
     * Store an order product with the buyer data.
     * @test
     * @return void
     */
    public function a_order_can_be_stored_and_use_placetopay()
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

        $newOrder = $product->getOrder();
        
        $placeToPay = $newOrder->gateway()->first();

        $dataGateway =  json_decode($placeToPay->payment_data,true);

        $this->assertDatabaseHas('orders',['number'=>$newOrder->number]);

        $this->assertEquals($product->id,$newOrder->product_id);

        $response->assertRedirect($dataGateway['process_url']);
    }

    /**
     * Validate the validation errors when store an order.
     * @test
     * @return void
     */
    public function a_stored_order_must_validate_data()
    {
        factory(Product::class,1)->create();

        $product = Product::first();

        $data = [
            'product_id'       => 80,
            'customer_name'    => null,
            'customer_email'   => null,
            'customer_mobile'  => null
        ];

        $response = $this->post('orders',$data);

        $response->assertSessionHasErrors([
            'product_id' => 'The selected product is invalid.',
            'customer_name',
            'customer_email',
            'customer_mobile',
        ]);

    }

}
