<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;

use App\Models\Cart;

use App\Models\CartItem;

use App\Models\Product;

use Illuminate\Http\Request;


/**
 * API controller for managing the authenticated user's shopping cart.
 */
class CartController extends Controller
{
    /**
     * Retrieve or create the cart for the given user.
     */
    protected function getCart($user): Cart
    {
        
        return Cart::firstOrCreate([
            
            'user_id' => $user->id,
        ]);
    }

    /**
     * View the current contents and subtotal of the authenticated user's cart.
     */
    public function view(Request $request)
    {
        
        $cart = $this->getCart($request->user());

        
        $items = $cart->items()->with('product')->get();

        
        $subtotal = $items->reduce(function ($carry, CartItem $item) {
            return $carry + ($item->unit_price * $item->quantity);
        }, 0);

        
        return response()->json([
            'items' => $items,
            'subtotal' => number_format($subtotal, 2, '.', ''),
        ]);
    }

    /**
     * Add a product to the cart or increase its quantity if it already exists.
     */
    public function add(Request $request)
    {
        
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        
        $quantity = $data['quantity'] ?? 1;

        
        $product = Product::findOrFail($data['product_id']);

        
        $cart = $this->getCart($request->user());

        
        $item = $cart->items()->where('product_id', $product->id)->first();

        
        if ($item) {
            $item->quantity += $quantity;
            $item->save();
        } else {
            
            $cart->items()->create([
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $product->price,
            ]);
        }

        
        return $this->view($request);
    }

    /**
     * Update the quantity of a product in the cart or remove it when quantity is zero.
     */
    public function update(Request $request)
    {
        
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:0',
        ]);

        
        $cart = $this->getCart($request->user());

        
        $item = $cart->items()->where('product_id', $data['product_id'])->first();

        
        if (! $item) {
            return response()->json(['message' => 'Item not found in cart'], 404);
        }

        
        if ($data['quantity'] === 0) {
            $item->delete();
        } else {
            
            $item->quantity = $data['quantity'];
            $item->save();
        }

        
        return $this->view($request);
    }

    /**
     * Remove a product from the authenticated user's cart.
     */
    public function remove(Request $request)
    {
        
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        
        $cart = $this->getCart($request->user());

        
        $cart->items()->where('product_id', $data['product_id'])->delete();

        
        return $this->view($request);
    }
}
