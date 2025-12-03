<?php

namespace App\Http\Controllers\Api; 

use App\Http\Controllers\Controller; 
use App\Models\Order; 
use Illuminate\Http\Request; 

/**
 * API controller for viewing customer and administrative orders.
 */
class OrderController extends Controller
{
    /**
     * Get the authenticated user's orders with their items and products.
     */
    public function myOrders(Request $request)
    {
        
        $orders = $request->user()
            
            ->orders()
            
            ->with('items.product')
            
            ->get();

        
        return response()->json($orders);
    }

    /**
     * List all orders with related data for admin users.
     */
    public function index(Request $request)
    {
        
        if (! $request->user()->is_admin) {
            
            return response()->json(['message' => 'Forbidden'], 403);
        }

        
        $orders = Order::with('items.product', 'user')->get();

        
        return response()->json($orders);
    }

    /**
     * Show a single order if it belongs to the user or the user is an admin.
     */
    public function show(Request $request, Order $order)
    {
        
        $user = $request->user();

        
        if ($user->id !== $order->user_id && ! $user->is_admin) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        
        $order->load('items.product');

        
        return response()->json($order);
    }
}
