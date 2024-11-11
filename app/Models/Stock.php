<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    // Si tu utilises les colonnes suivantes pour gérer ton stock
    protected $fillable = ['product_id', 'quantity'];

    // Relation inverse avec le produit (one-to-one)
    public function product() {
        return $this->belongsTo(Product::class);
    }
}
