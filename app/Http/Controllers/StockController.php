<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Product;
use Illuminate\Http\Request;

class StockController extends Controller
{
    /**
     * Mettre à jour la quantité de stock pour un produit spécifique.
     */
    public function update(Request $request, $product_id) {
        // Valider la quantité
        $validated = $request->validate([
            'quantity' => 'required|integer|min:0', // Validation de la quantité
        ]);

        // Trouver le produit correspondant
        $product = Product::findOrFail($product_id);

        // Vérifier si un stock existe déjà pour ce produit
        $stock = $product->stock;

        if ($stock) {
            // Si un stock existe, mettre à jour la quantité
            $stock->quantity = $validated['quantity'];
            $stock->save();
        } else {
            // Si aucun stock n'existe, créer un nouveau stock pour ce produit
            Stock::create([
                'product_id' => $product->id,
                'quantity' => $validated['quantity']
            ]);
        }

        // Rediriger vers la page d'accueil après la mise à jour
        return redirect()->route('home')->with('success', 'Stock updated successfully!');
    }
}
