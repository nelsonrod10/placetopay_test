<?php

namespace Tests\Feature;

use App\User;
use App\Order;
use App\Product;
use Tests\TestCase;
use App\PaymentGateway;
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
            'product_id'    => 'El producto no existe.',
            'customer_name' => 'El campo nombre es obligatorio.',
            'customer_email'=> 'El campo email es obligatorio.',
            'customer_mobile' => 'El campo número celular es obligatorio.',
        ]);

    }

    /**
     * Show order summary to customer, before proceed to gateway pay.
     * @test
     * @return void
     */
    public function show_a_order_summary_to_customer()
    {
        $processUrl = "https://www.testapp.com";
        $requestId  = 8090100;

        factory(Product::class,1)->create();
        $product = Product::first();

        factory(Order::class)->create([
            'product_id' => $product->id,
        ])->each(function($order) use($processUrl,$requestId) {
            factory(PaymentGateway::class)->create([
                'order_id' => $order->id,
                'enterprise' => 'Place to pay',
                'payment_data' => json_encode([
                    'process_url' => $processUrl,
                    'request_id'  => $requestId,
                    'status'      => 'PENDING'      
                ]),
            ]);
        });
        
        $newOrder = $product->getOrder();

        $response = $this->get('orders/'.$newOrder->id);

        $response->assertStatus(200);

        $response->assertViewIs('orders.show');
        $response->assertViewHas([
            'order'=> $newOrder,
            'payment' => [
                'process_url' => $processUrl,
                'request_id'  => $requestId,
                'status'      => 'PENDING'      
            ]
        ]);

    }

    /**
     * Show orders index for admin user.
     * @test
     * @return void
     */
    public function show_a_orders_index_to_admin()
    {
        $this->withoutExceptionHandling();

        $user = factory(User::class)->create([
            'role' => 'Admin'
        ]);

        factory(Product::class,5)
        ->create()
        ->each(function($product){
            factory(Order::class)
            ->create([
                'product_id' => $product->id
            ]);
        });    

        $response = $this->actingAs($user)
                    ->get('orders-list');

        $response->assertStatus(200);
        
        $orders = Order::all();
        
        $response->assertViewIs('orders.index');

        $response->assertViewHas(['orders' => $orders]);
    }

}
