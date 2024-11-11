<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

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

    // Routes pour le stock
    Route::get('stock/{product_id}', [StockController::class, 'show']);  // Afficher le stock d'un produit
    Route::put('stock/{product_id}', [StockController::class, 'update']);  // Mettre à jour le stock d'un produit
});