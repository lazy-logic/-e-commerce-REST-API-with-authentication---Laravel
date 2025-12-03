<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;


/**
 * Eloquent model representing a placed order.
 */
class Order extends Model
{
    
    use HasFactory;

    /**
     * Attributes that are mass assignable on the order.
     *
     * @var array<int, string>
     */
    protected $fillable = ['user_id', 'total', 'status'];

    
    /**
     * Get the user who placed the order.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    
    /**
     * Get the line items associated with the order.
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
