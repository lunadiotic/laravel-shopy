<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ProductController extends Controller implements HasMiddleware
{

    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum', except: ['index', 'show']),
        ];
    }

    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10); // Default 10 items per page
        $products = Product::paginate($perPage);

        return response()->json($products);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
        ]);

        if ($request->user()->role !== 'seller') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $product = $request->user()->products()->create($request->all());

        return response()->json($product, 201);
    }

    public function show(Product $product)
    {
        return response()->json($product);
    }

    public function update(Request $request, Product $product)
    {
        if ($request->user()->id !== $product->user_id) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'name' => 'string|max:255',
            'description' => 'string',
            'price' => 'numeric',
            'quantity' => 'integer',
        ]);

        $product->update($request->all());

        return response()->json($product);
    }

    public function destroy(Request $request, Product $product)
    {
        if ($request->user()->id !== $product->user_id) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $product->delete();

        return response()->json(null, 204);
    }
}