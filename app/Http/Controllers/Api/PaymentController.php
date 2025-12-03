<?php

namespace App\Http\Controllers\Api; 

use App\Http\Controllers\Controller; 
use App\Models\Order; 
use Illuminate\Http\Request; 

/**
 * API controller for simulating order payment confirmation.
 */
class PaymentController extends Controller
{
    /**
     * Mark an order as paid after validating access for the current user.
     */
    public function simulate(Request $request)
    {
        
        $data = $request->validate([
            'order_id' => 'required|exists:orders,id',
        ]);

        
        $order = Order::findOrFail($data['order_id']);

        
        $user = $request->user();

        
        if ($order->user_id !== $user->id && ! $user->is_admin) {
            
            return response()->json(['message' => 'Forbidden'], 403);
        }

        
        $order->status = 'paid'; 
        $order->save(); 

        
        return response()->json([
            'payment_successful' => true,
            'order' => $order,
        ]);
    }
}
