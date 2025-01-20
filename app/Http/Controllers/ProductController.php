<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    private $filePath = 'products.json';

    public function index()
    {
        return view('products.index');
    }

    public function store(Request $request)
    {
        $request->validate([

            'product_name' => 'required|string',
            'quantity' => 'required|integer',
            'price' => 'required|numeric',
        ]);

        $data = [
            'id' => uniqid(),
            'product_name' => $request->product_name,
            'quantity' => $request->quantity,
            'price' => $request->price,
            'datetime_submitted' => now(),
            'total_value' => $request->quantity * $request->price,
        ];

        // Save to JSON file
        $this->saveToFile($data);

        return response()->json($data);
    }

    public function getProducts()
    {
        $products = $this->getFromFile();
        return response()->json($products);
    }

    public function update(Request $request, $id)
    {
        $request->validate([

            'product_name' => 'required|string',
            'quantity' => 'required|integer',
            'price' => 'required|numeric',
        ]);

        $products = $this->getFromFile();
        foreach ($products as &$product) {
            if ($product['id'] === $id) {
                $product['product_name'] = $request->input('product_name');
                $product['quantity'] = $request->input('quantity');
                $product['price'] = $request->input('price');
                $product['total_value'] = $request->input('quantity') * $request->input('price');
                $product['datetime_submitted'] = now();
                break;
            }
        }

        // Save updated products back to the JSON file
        Storage::disk('local')->put($this->filePath, json_encode($products, JSON_PRETTY_PRINT));

        return response()->json($products);
    }

    private function saveToFile($data)
    {
        $products = $this->getFromFile();
        $products[] = $data;
        Storage::disk('local')->put($this->filePath, json_encode($products, JSON_PRETTY_PRINT));
    }

    private function getFromFile()
    {
        if (!Storage::disk('local')->exists($this->filePath)) {
            return [];
        }
        return json_decode(Storage::disk('local')->get($this->filePath), true);
    }
}
