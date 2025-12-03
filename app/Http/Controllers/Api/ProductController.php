<?php

namespace App\Http\Controllers\Api; 

use App\Http\Controllers\Controller; 
use App\Models\Product; 
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Storage; 


/**
 * API controller for listing and managing products.
 */
class ProductController extends Controller
{
    /**
     * Return a paginated list of products.
     */
    public function index()
    {
        
        $products = Product::paginate(12);

        
        return response()->json($products);
    }

    /**
     * Show a single product resource.
     */
    public function show(Product $product)
    {
        
        return response()->json($product);
    }

    /**
     * Create a new product.
     *
     * Only authenticated admin users may perform this action.
     */
    public function store(Request $request)
    {
        
        if (! $request->user()->is_admin) {
            
            return response()->json(['message' => 'Forbidden'], 403);
        }

        
        $data = $request->validate([
            'name' => 'required|string', 
            'description' => 'nullable|string', 
            'price' => 'required|numeric|min:0', 
            'stock' => 'required|integer|min:0', 
            'image' => 'nullable|image|max:2048', 
        ]);

        
        $imagePath = null;

        
        if ($request->hasFile('image')) {
            
            $imagePath = $request->file('image')->store('products', 'public');
        }

        
        $product = Product::create([
            'name' => $data['name'], 
            'description' => $data['description'] ?? null, 
            'price' => $data['price'], 
            'stock' => $data['stock'], 
            'image_url' => $imagePath, 
        ]);

        
        return response()->json($product, 201);
    }

    /**
     * Update an existing product.
     *
     * Only authenticated admin users may perform this action.
     */
    public function update(Request $request, Product $product)
    {
        
        if (! $request->user()->is_admin) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        
        $data = $request->validate([
            'name' => 'sometimes|required|string', 
            'description' => 'nullable|string', 
            'price' => 'sometimes|required|numeric|min:0', 
            'stock' => 'sometimes|required|integer|min:0', 
            'image' => 'nullable|image|max:2048', 
        ]);

        
        if ($request->hasFile('image')) {
            
            if ($product->image_url) {
                Storage::disk('public')->delete($product->image_url);
            }

            
            $imagePath = $request->file('image')->store('products', 'public');
            $data['image_url'] = $imagePath;
        }

        
        $product->update($data);

        
        return response()->json($product);
    }

    /**
     * Delete an existing product.
     *
     * Only authenticated admin users may perform this action.
     */
    public function destroy(Request $request, Product $product)
    {
        
        if (! $request->user()->is_admin) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        
        if ($product->image_url) {
            Storage::disk('public')->delete($product->image_url);
        }

        
        $product->delete();

        
        return response()->json(['message' => 'Product deleted']);
    }
}
