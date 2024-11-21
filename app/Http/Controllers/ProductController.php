<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Stock;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Affiche tous les produits pour la page d'accueil
     */
    public function index(Request $request) {
        // Récupérer toutes les catégories
        $categories = Category::all();

        // Vérifier si un filtre de catégorie est appliqué
        $categoryFilter = $request->get('category_id');

        // Récupérer les produits et appliquer un filtre de catégorie si présent
        $products = Product::with('category')
            ->when($categoryFilter, function ($query) use ($categoryFilter) {
                return $query->where('category_id', $categoryFilter);
            })
            ->orderBy('category_id')
            ->get();

        // Gestion des dépôts (en dur pour l'exemple)
        $depots = [
            ['name' => 'Paris', 'color' => 'blue'],
            ['name' => 'Colombier', 'color' => 'orange'],
            ['name' => 'Vaulx en Velin', 'color' => 'pink'],
            ['name' => 'Pouilly', 'color' => 'yellow'],
            ['name' => 'Lyon', 'color' => 'red'],
        ];

        // Tailles disponibles (35 à 50)
        $sizes = range(35, 50);

        // Préparer les données des stocks
        $stocks = [];
        foreach ($sizes as $size) {
            foreach ($depots as $depot) {
                foreach ($products as $product) {
                    $stocks[$size][$depot['name']][$product->id] = Stock::where('product_id', $product->id)
                        ->where('size', $size)
                        ->where('depot', $depot['name'])
                        ->value('quantity') ?? 0;
                }
            }
        }

        // Retourner la vue avec les données
        return view('home', compact('products', 'categories', 'depots', 'sizes', 'stocks'));
    }

    /**
     * Affiche un produit spécifique (API)
     */
    public function show($id) {
        return response()->json(Product::with('category')->findOrFail($id));
    }

    /**
     * Ajouter un nouveau produit (API)
     */
    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string|max:1000',
            'size' => 'required|string|max:10',
        ]);

        // Générer le SKU
        $sku = strtolower(str_replace(' ', '-', $validated['name'])) . '-' . strtolower($validated['size']);

        // Créer le produit
        Product::create([
            'name' => $validated['name'],
            'price' => $validated['price'],
            'category_id' => $validated['category_id'],
            'description' => $validated['description'] ?? null,
            'size' => $validated['size'],
            'sku' => $sku,
        ]);

        return redirect()->route('home')->with('success', 'Product added successfully!');
    }

    /**
     * Mettre à jour un produit (API)
     */
    public function update(Request $request, $id) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'size' => 'required|string|max:10',
        ]);

        $product = Product::findOrFail($id);

        // Mise à jour du produit
        $product->update([
            'name' => $validated['name'],
            'price' => $validated['price'],
            'category_id' => $validated['category_id'],
            'description' => $validated['description'] ?? null,
            'size' => $validated['size'],
            'sku' => strtolower(str_replace(' ', '-', $validated['name'])) . '-' . strtolower($validated['size']),
        ]);

        return response()->json($product);
    }

    /**
     * Supprimer un produit (API)
     */
    public function destroy($id) {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->route('home')->with('success', 'Product deleted successfully!');
    }
}
