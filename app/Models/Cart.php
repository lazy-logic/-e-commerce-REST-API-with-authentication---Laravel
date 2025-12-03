<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;


/**
 * Eloquent model representing a shopping cart owned by a user.
 */
class Cart extends Model
{
    
    use HasFactory;

    /**
     * Attributes that are mass assignable on the cart.
     *
     * @var array<int, string>
     */
    protected $fillable = ['user_id'];

    
    /**
     * Get the user that owns the cart.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    
    /**
     * Get the items contained in the cart.
     */
    public function items()
    {
        return $this->hasMany(CartItem::class);
    }
}
