<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();
        // Set up a fake disk for testing
        Storage::fake('local');
    }

    /** @test */
    public function it_can_store_a_product()
    {
        $data = [
            'product_name' => 'Test Product',
            'quantity' => 10,
            'price' => 9.99,
        ];

        $response = $this->post('/products', $data);

        $response->assertStatus(200);
        $this->assertCount(1, json_decode(Storage::get('products.json'), true));
        $this->assertStringContainsString('Test Product', Storage::get('products.json'));
    }

    /** @test */
    public function it_can_get_all_products()
    {
        // Simulate existing products in the JSON file
        Storage::put('products.json', json_encode([
            [
                'id' => '1',
                'product_name' => 'Test Product 1',
                'quantity' => 5,
                'price' => 15.00,
                'datetime_submitted' => now(),
                'total_value' => 75.00,
            ],
            [
                'id' => '2',
                'product_name' => 'Test Product 2',
                'quantity' => 3,
                'price' => 20.00,
                'datetime_submitted' => now(),
                'total_value' => 60.00,
            ],
        ]));

        $response = $this->get('/products');

        $response->assertStatus(200);
        $response->assertJsonCount(2);
    }


}
