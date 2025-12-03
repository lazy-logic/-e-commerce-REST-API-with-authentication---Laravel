<?php

namespace App\Http\Controllers\Api; 

use App\Http\Controllers\Controller; 
use App\Models\Product; 
use Illuminate\Http\Request; 

/**
 * API controller for administrative product management actions.
 */
class AdminController extends Controller
{
    /**
     * Restock a product by increasing its available inventory.
     *
     * Only authenticated admin users may perform this action.
     */
    public function restock(Request $request)
    {
        
        if (! $request->user()->is_admin) {
            
            return response()->json(['message' => 'Forbidden'], 403);
        }

        
        $data = $request->validate([
            'product_id' => 'required|exists:products,id', 
            'amount' => 'required|integer|min:1', 
        ]);

        
        $product = Product::findOrFail($data['product_id']);

        
        $product->stock += $data['amount'];
        
        $product->save();

        
        return response()->json($product);
    }
}
