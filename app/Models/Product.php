<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'price', 'category_id', 'size'];

    // Relation avec la catégorie (one-to-many inverse)
    public function category() {
        return $this->belongsTo(Category::class);
    }

    // Relation avec le stock (one-to-one)
    public function stock() {
        return $this->hasOne(Stock::class);
    }
}
