<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;


/**
 * Eloquent model representing a product available for purchase.
 */
class Product extends Model
{
    
    use HasFactory;

    /**
     * Attributes that are mass assignable on the product.
     *
     * @var array<int, string>
     */
    protected $fillable = ['name', 'description', 'price', 'stock', 'image_url'];

    /**
     * Get cart items that reference this product.
     */
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Get order items that reference this product.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
