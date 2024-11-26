<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stock;  // ou votre modèle de stock

class StockController extends Controller
{
    public function updateStock(Request $request)
{
    $updates = $request->json()->all();

    \Log::debug('Données reçues pour mise à jour des stocks:', $updates);


    try {
        foreach ($updates as $update) {
            // Vérification des données
            if (!isset($update['product_id'], $update['depot'], $update['size'], $update['quantity'])) {
                throw new \Exception("Données manquantes pour la mise à jour du stock.");
            }

            $stock = Stock::where('product_id', $update['product_id'])
                          ->where('depot', $update['depot'])
                          ->where('size', $update['size'])
                          ->first();

            if ($stock) {
                $stock->quantity = $update['quantity'];
                $stock->save();
            } else {
                Stock::create([
                    'product_id' => $update['product_id'],
                    'depot' => $update['depot'],
                    'size' => $update['size'],
                    'quantity' => $update['quantity'],
                ]);
            }
        }
        
        return response()->json(['message' => 'Stocks mis à jour avec succès.']);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Erreur lors de la mise à jour des stocks : ' . $e->getMessage()], 500);
    }
}

}
