<?php

namespace Tests\Feature;

use App\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductTest extends TestCase
{
    
    use RefreshDatabase;
    /**
     * Store a product in the database.
     * @test
     * @return void
     */
    public function canCreateSingleProduct()
    {
        $this->withoutExceptionHandling();
        $data = [
            'name'        => 'Product test',
            'price'       => '850000',
            'currency'    => 'COP',
            'description' => 'This is the best product'
        ];

        $response = $this->post('products',$data);
        
        $product = Product::where('name',$data['name'])->first();

        $this->assertDatabaseHas('products',['name' => $product->name]);    
        
        $response->assertRedirect('products/'.$product->id);
    }

    /**
     * View all products in the store.
     * @test
     * @return void
     */
    public function listAndViewAllProducts()
    {
        $this->withoutExceptionHandling();

        factory(Product::class,10)->make();

        $response = $this->get('products');
        
        $response->assertStatus(200);
        
        $products = Product::all();

        $response->assertViewIs('index');

        $response->assertViewHas('products',$products);

    }
}
