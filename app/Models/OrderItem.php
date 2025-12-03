<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Eloquent model representing a single line item within an order.
 */
class OrderItem extends Model
{
    use HasFactory;

    /**
     * Attributes that can be mass assigned on the order item.
     *
     * @var array<int, string>
     */
    protected $fillable = ['order_id', 'product_id', 'quantity', 'unit_price'];

    /**
     * Get the order that owns this order item.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the product associated with this order item.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
