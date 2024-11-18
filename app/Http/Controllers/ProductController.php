<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category; // Ajout pour gérer les catégories
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

        // Récupérer les produits avec leurs catégories, et appliquer un filtre de catégorie si présent
        $products = Product::with('category')
            ->when($categoryFilter, function ($query) use ($categoryFilter) {
                return $query->where('category_id', $categoryFilter); // Appliquer le filtre
            })
            ->orderBy('category_id')  // Trier par catégorie
            ->get();

        // Passer les produits et les catégories à la vue
        return view('home', compact('products', 'categories'));
    }

    /**
     * Affiche un produit spécifique (API)
     */
    public function show($id) {
        // Retourne les informations d'un produit avec sa catégorie
        return response()->json(Product::with('category')->findOrFail($id));
    }

    /**
     * Ajouter un nouveau produit (API)
     */
    public function store(Request $request) {
        // Validation des données envoyées
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id', // Vérifier que la catégorie existe
            'description' => 'nullable|string|max:1000', // Description est facultative
            'size' => 'required|string|max:10',  // Taille obligatoire, chaîne de caractères
        ]);
    
        // Générer le SKU basé sur le nom et la taille (utilisation de `strtolower` pour normaliser)
        $sku = strtolower(str_replace(' ', '-', $validated['name'])) . '-' . strtolower($validated['size']);
    
        // Créer le produit avec les données validées
        $product = Product::create([
            'name' => $validated['name'],
            'price' => $validated['price'],
            'category_id' => $validated['category_id'],
            'description' => $validated['description'] ?? null,  // Assurez-vous que description est incluse et soit null si vide
            'size' => $validated['size'],  // Taille ajoutée ici
            'sku' => $sku,  // SKU généré
        ]);
    
        // Rediriger vers la page d'accueil après ajout
        return redirect()->route('home')->with('success', 'Product added successfully!');
    }
    
    /**
     * Mettre à jour un produit (API)
     */
    public function update(Request $request, $id) {
        // Validation des données de mise à jour
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000', // Description facultative
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'size' => 'required|string|max:10', // Taille obligatoire, chaîne de caractères
        ]);
    
        // Trouver le produit à mettre à jour
        $product = Product::findOrFail($id);
        
        // Mettre à jour les informations du produit
        $product->update([
            'name' => $validated['name'],
            'price' => $validated['price'],
            'category_id' => $validated['category_id'],
            'description' => $validated['description'] ?? null,  // Gérer la description (nullable)
            'size' => $validated['size'],  // Taille mise à jour
            'sku' => strtolower(str_replace(' ', '-', $validated['name'])) . '-' . strtolower($validated['size']), // Mise à jour du SKU
        ]);
    
        // Retourner une réponse JSON avec le produit mis à jour
        return response()->json($product);
    }

    /**
     * Supprimer un produit (API)
     */
    public function destroy($id) {
        // Trouver le produit à supprimer
        $product = Product::findOrFail($id);
        
        // Supprimer le produit
        $product->delete();

        // Rediriger vers la page d'accueil
        return redirect()->route('home');
    }
}
