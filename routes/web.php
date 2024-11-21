<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\DepotController;


// Page d'accueil
Route::get('/', function () {
    return view('welcome');
});

// Page d'accueil (afficher les produits)
Route::get('/home', [ProductController::class, 'index'])->name('home');

// Authentification
Route::view('/login', 'auth')->name('login');  // Page de login
Route::post('/login', [AuthController::class, 'login']);  // Route de connexion (POST)
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');  // Déconnexion avec middleware Sanctum

// Utilisateur authentifié
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response()->json(['user' => $request->user()]);
});

// Routes API regroupées sous le préfixe "api"
Route::prefix('api')->group(function () {
    // Test de l'API
    Route::get('test', function () {
        return response()->json(['message' => 'API is working']);
    });

    // Routes pour les produits
    Route::get('products', [ProductController::class, 'index']);  // Liste des produits
    Route::get('products/{id}', [ProductController::class, 'show']);  // Afficher un produit
    Route::post('products', [ProductController::class, 'store'])->name('products.store');  // Créer un produit
    Route::put('products/{id}', [ProductController::class, 'update']);  // Mettre à jour un produit
    Route::delete('products/{id}', [ProductController::class, 'destroy']);  // Supprimer un produit

    // Routes pour la gestion du stock
Route::get('stock/{product_id}', [StockController::class, 'show']);  // Afficher le stock d'un produit
Route::patch('stock/{product_id}', [StockController::class, 'update'])->name('stock.update');
Route::get('stock/{product_id}/edit', [StockController::class, 'edit'])->name('stock.edit');



    // Routes pour le stock
    // Routes API pour les produits
Route::prefix('api')->group(function () {
    // Afficher tous les produits
    Route::get('products', [ProductController::class, 'index']);

    // Afficher un produit spécifique
    Route::get('products/{id}', [ProductController::class, 'show']);

    // Créer un nouveau produit
    Route::post('products', [ProductController::class, 'store'])->name('products.store');

    // Mettre à jour un produit
    Route::put('products/{id}', [ProductController::class, 'update'])->name('products.update');

    // Supprimer un produit (route manquante à définir)
    Route::delete('products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');  // Route pour supprimer un produit
});

    // Route pour afficher les catégories
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');

    // Route pour ajouter une nouvelle catégorie
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');

    // Route pour supprimer une catégorie
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');

});

// Routes pour les dépôts
Route::resource('depots', DepotController::class);

Route::post('/update-stock', [StockController::class, 'update'])->name('stock.update');
