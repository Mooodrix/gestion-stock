<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Product;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function show($product_id) {
        $stock = Stock::where('product_id', $product_id)->first();
        return response()->json($stock);
    }

    public function update(Request $request, $product_id) {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        $stock = Stock::where('product_id', $product_id)->first();

        if ($stock) {
            $stock->update($validated);
        } else {
            Stock::create([
                'product_id' => $product_id,
                'quantity' => $validated['quantity']
            ]);
        }

        return response()->json($stock);
    }
}
