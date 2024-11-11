<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category; // Ajoutez cette ligne pour Category
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Affiche tous les produits pour la page d'accueil
    // Dans ProductController.php

        public function index() {
            // Récupérer tous les produits avec leurs catégories et stocks
            $products = Product::with('category', 'stock')->get();
            
            // Récupérer toutes les catégories
            $categories = Category::all();

            // Passer les produits et les catégories à la vue
            return view('home', compact('products', 'categories'));
        }


    // Affiche un produit spécifique (API)
    public function show($id)
    {
        return response()->json(Product::with('category')->findOrFail($id));
    }

    // Ajouter un nouveau produit (API)
    public function store(Request $request) {
        // Valider les données envoyées
        $validated = $request->validate([
            'name' => 'required',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable',
            'size' => 'required',  // Taille obligatoire
        ]);
    
        // Générer le SKU basé sur le nom et la taille
        $sku = strtolower(str_replace(' ', '-', $request->name)) . '-' . strtolower($request->size);
    
        // Créer le produit avec les données validées
        $product = Product::create([
            'name' => $validated['name'],
            'price' => $validated['price'],
            'category_id' => $validated['category_id'],
            'description' => $validated['description'],  // Ajouter la description
            'sku' => $sku,  // SKU généré
        ]);
    
        // Rediriger vers la page d'accueil après ajout
        return redirect()->route('home');  
    }
    

    // Mettre à jour un produit (API)
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required',
            'description' => 'nullable',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'sku' => 'required|unique:products,sku,' . $id,
        ]);

        $product = Product::findOrFail($id);
        $product->update($validated);
        return response()->json($product);
    }

    // Supprimer un produit (API)
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return response()->json(null, 204);
    }
}
