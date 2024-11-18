<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Depot;
use App\Models\Product; // Si vous avez un modèle Product pour gérer les produits

class DepotController extends Controller
{
    // Afficher la liste des dépôts
    public function index()
    {
        $depots = Depot::all(); // Récupérer tous les dépôts
        $products = Product::all(); // Récupérer tous les produits si vous en avez un modèle 'Product'
        return view('depots.index', compact('depots', 'products')); // Passer les variables 'depots' et 'products' à la vue
    }

    // Afficher le formulaire de création d'un dépôt
    public function create()
    {
        return view('depots.create'); // Vue avec le formulaire de création
    }

    // Enregistrer un nouveau dépôt
    public function store(Request $request)
    {
        // Validation des données entrantes
        $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:7', // Exemple : #ff0000
            'location' => 'nullable|string|max:255', // Localisation du dépôt (optionnel)
        ]);

        // Création du dépôt
        Depot::create([
            'name' => $request->name,
            'color' => $request->color,
            'location' => $request->location,
        ]);

        return redirect()->route('depots.index')->with('success', 'Depot créé avec succès.');
    }

    // Afficher un dépôt spécifique (optionnel)
    public function show($id)
    {
        $depot = Depot::findOrFail($id);
        return view('depots.show', compact('depot'));
    }

    // Afficher le formulaire d'édition d'un dépôt
    public function edit($id)
    {
        $depot = Depot::findOrFail($id);
        return view('depots.edit', compact('depot'));
    }

    // Mettre à jour un dépôt existant
    public function update(Request $request, $id)
    {
        // Validation des données
        $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:7',
            'location' => 'nullable|string|max:255',
        ]);

        // Mise à jour du dépôt
        $depot = Depot::findOrFail($id);
        $depot->update([
            'name' => $request->name,
            'color' => $request->color,
            'location' => $request->location,
        ]);

        return redirect()->route('depots.index')->with('success', 'Depot mis à jour avec succès.');
    }

    // Supprimer un dépôt
    public function destroy($id)
    {
        $depot = Depot::findOrFail($id);
        $depot->delete();

        return redirect()->route('depots.index')->with('success', 'Depot supprimé avec succès.');
    }

    // Optionnel : Mettre à jour les stocks pour un produit dans un dépôt
    public function updateStock(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'depot_id' => 'required|exists:depots,id',
            'quantity' => 'required|integer|min:0',
        ]);

        // Recherche du produit et de son stock
        $product = Product::findOrFail($request->product_id);
        $depot = Depot::findOrFail($request->depot_id);

        // Mettez à jour le stock dans la table intermédiaire (par exemple, stocks)
        $product->stocks()->updateOrCreate(
            ['depot_id' => $depot->id],
            ['quantity' => $request->quantity]
        );

        return response()->json(['success' => true, 'message' => 'Stock mis à jour avec succès.']);
    }
}
