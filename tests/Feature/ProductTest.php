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
    public function can_create_single_product()
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
     * Validate data before to store new product.
     * @test
     * @return void
     */
    public function validate_data_before_store_a_product()
    {
        $this->withoutExceptionHandling();

        $response = $this->post('products',[
            'name'        => 'The name on the product',
            'price'       => '950000',
            'currency'    => 'COP',
            'description' => 'the short description'
        ])->assertSessionHasNoErrors([
            'name',        
            'price',       
            'currency',
            'description', 
        ]);
        
    }

    /**
     * View all products in the store.
     * @test
     * @return void
     */
    public function list_and_view_all_products()
    {
        $this->withoutExceptionHandling();

        factory(Product::class,10)->make();

        $response = $this->get('products');
        
        $response->assertStatus(200);
        
        $products = Product::all();

        $response->assertViewIs('index');

        $response->assertViewHas('products',$products);

    }

    /**
     * View a specific product in the store.
     * @test
     * @return void
     */
    public function can_show_a_product()
    {
        $this->withoutExceptionHandling();

        factory(Product::class,1)->create();

        $product = Product::first();

        $response = $this->get('products/'.$product->id);
        
        $response->assertStatus(200);

        $response->assertViewIs('products.show');

        $response->assertViewHas('product',$product);

    }
}
