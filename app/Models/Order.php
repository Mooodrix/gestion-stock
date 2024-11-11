<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'total_price', 'status'
    ];

    // Relation avec l'utilisateur (User)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relation avec les articles de commande (OrderItem)
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
