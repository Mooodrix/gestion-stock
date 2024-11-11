<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Stock;      // Ajoutez cette ligne pour Stockuse App\Models\Stock;      // Ajoutez cette ligne pour Stock
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function store(Request $request) {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);
    
        $order = Order::create([
            'user_id' => $validated['user_id'],
            'total_price' => collect($validated['items'])->sum(function ($item) {
                return $item['quantity'] * Product::find($item['product_id'])->price;
            }),
        ]);
    
        foreach ($validated['items'] as $item) {
            $product = Product::findOrFail($item['product_id']);
            $stock = Stock::where('product_id', $product->id)->first();
    
            // Vérification de la disponibilité du stock
            if ($stock && $stock->quantity < $item['quantity']) {
                return response()->json(['error' => 'Not enough stock for product ' . $product->name], 400);
            }
    
            // Créer l'item de commande
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'price' => $product->price,
            ]);
    
            // Mettre à jour le stock
            if ($stock) {
                $stock->decrement('quantity', $item['quantity']);
            }
        }
    
        return response()->json($order, 201);
    }
    
}
