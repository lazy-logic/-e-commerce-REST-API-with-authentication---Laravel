<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Eloquent model representing a single line item in a shopping cart.
 */
class CartItem extends Model
{
    use HasFactory;

    /**
     * Attributes that can be mass assigned on the cart item.
     *
     * @var array<int, string>
     */
    protected $fillable = ['cart_id', 'product_id', 'quantity', 'unit_price'];

    /**
     * Get the cart that owns this cart item.
     */
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * Get the product associated with this cart item.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
