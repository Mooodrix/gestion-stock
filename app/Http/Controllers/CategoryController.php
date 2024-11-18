<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // Ajouter une nouvelle catégorie
    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|unique:categories,name',
        ]);

        Category::create($validated);

        return redirect()->route('home')->with('success', 'Category added successfully');
    }
    public function destroy($id)
{
    // Trouver la catégorie par son ID
    $category = Category::findOrFail($id);

    // Supprimer la catégorie
    $category->delete();

    // Rediriger l'utilisateur avec un message de succès
    return redirect()->route('home')->with('success', 'Category deleted successfully');
}
public function index()
{
    // Récupérer toutes les catégories
    $categories = Category::all();

    // Retourner la vue avec les catégories
    return view('categories.index', compact('categories'));
}

}
