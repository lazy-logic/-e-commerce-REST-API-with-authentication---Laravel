<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;

use App\Models\Order;

use App\Models\OrderItem;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;


/**
 * API controller for converting a user's cart into an order (checkout).
 */
class CheckoutController extends Controller
{
    /**
     * Create an order from the authenticated user's cart in a database transaction.
     */
    public function checkout(Request $request)
    {
        
        $user = $request->user();

        
        $cart = $user->cart()->with('items.product')->first();

        
        if (! $cart || $cart->items->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 400);
        }

        
        return DB::transaction(function () use ($user, $cart) {
            
            $items = $cart->items()->with('product')->get();

            
            foreach ($items as $item) {
                
                $product = $item->product;

                
                if ($item->quantity > $product->stock) {
                    return response()->json([
                        'message' => "Insufficient stock for product: {$product->name}",
                    ], 400);
                }
            }

            
            $total = 0;

            
            foreach ($items as $item) {
                $total += $item->unit_price * $item->quantity;
            }

            
            $order = Order::create([
                'user_id' => $user->id,
                'total' => $total,
                'status' => 'pending', 
            ]);

            
            foreach ($items as $item) {
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                ]);

                
                $product = $item->product;
                $product->stock = $product->stock - $item->quantity;
                $product->save();
            }

            
            $cart->items()->delete();

            
            $order->load('items.product');

            
            return response()->json([
                'order' => $order,
                'message' => 'Order created. Call /api/payment/simulate to confirm payment.',
            ], 201);
        });
    }
}
