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
     * A basic feature test example.
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
}
