<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Product;
use Illuminate\Http\Request;

class StockController extends Controller
{
    /**
     * Afficher le stock pour un produit spécifique.
     */
    public function show($product_id) {
        $product = Product::with('stock')->findOrFail($product_id);
        return view('stocks.show', compact('product'));
    }

    /**
     * Mettre à jour le stock pour un produit spécifique.
     */
    public function update(Request $request, $product_id)
{
    // Valider les données reçues
    $validated = $request->validate([
        'size' => 'required|numeric', // Taille obligatoire
        'depot' => 'required|string', // Dépôt obligatoire
        'quantity' => 'required|integer|min:0', // Quantité obligatoire
    ]);

    // Trouver le produit correspondant
    $product = Product::findOrFail($product_id);

    // Rechercher un stock existant pour ce produit, taille et dépôt
    $stock = Stock::where('product_id', $product->id)
        ->where('size', $validated['size'])
        ->where('depot', $validated['depot'])
        ->first();

    if ($stock) {
        // Si le stock existe, mettre à jour la quantité
        $stock->quantity += $validated['quantity']; // Ajouter à la quantité existante
        $stock->save();
    } else {
        // Si le stock n'existe pas, créer une nouvelle entrée
        Stock::create([
            'product_id' => $product->id,
            'size' => $validated['size'],
            'depot' => $validated['depot'],
            'quantity' => $validated['quantity'],
        ]);
    }

    // Rediriger avec un message de succès
    return redirect()->route('home')->with('success', 'Stock updated successfully!');
}


    /**
     * Préparer la vue pour éditer le stock.
     */
    public function edit($product_id) {
        $product = Product::with('stock')->findOrFail($product_id);
        $depots = ['Paris', 'Colombier', 'Vaulx en Velin', 'Pouilly', 'Lyon'];
        $sizes = range(35, 50);

        return view('stocks.edit', compact('product', 'depots', 'sizes'));
    }
}
