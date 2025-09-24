<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'name', 'description', 'price', 'image', 'is_flash_sale',
    ];

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }
}
