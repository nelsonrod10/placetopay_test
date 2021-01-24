<?php

use App\User;
use App\Order;
use App\Product;
use App\PaymentGateway;
use Illuminate\Support\Arr;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        factory(User::class)->create([
            'role' => 'Admin',
            'name' => 'Administrator',
            'email' => 'admin@email.com',
        ]);


        factory(Product::class,3)->create()
        ->each(function($product){
            factory(Order::class)->create([
                'product_id' => $product->id
            ])->each(function($order){
                factory(PaymentGateway::class)->create([
                    'order_id'  => $order->id,
                    'enterprise' => 'Place to pay',
                    'payment_data' => json_encode([
                        'process_url' => "https://www.testapp.com",
                        'request_id'  => rand(100,2000),
                        'status'      => Arr::random(['PENDING','APPROVED','REJECTED'])      
                    ])
                ]);
            });
        });
    }
}
