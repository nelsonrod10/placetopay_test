<?php

namespace Tests\Feature;

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

        $response = $this->post('products',[
            'name'        => 'Product test',
            'price'       => '850000',
            'currency'    => 'COP',
            'description' => 'This is the best product'
        ]);
dd($response);
        $response->assertStatus(200);
    }
}
