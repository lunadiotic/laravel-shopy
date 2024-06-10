<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Traits\CheckOwnership;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;

class OrderController extends Controller implements HasMiddleware
{
    use CheckOwnership;

    /**
     * Get the middleware that should be assigned to the controller.
     * @return array
     */
    public static function middleware(): array
    {
        return [
            'auth:sanctum',
        ];
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $orders = Order::where('user_id', $request->user()->id)->paginate(10);
        return response()->json($orders);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);
        $totalPrice = $product->price * $request->quantity;

        $order = Order::create([
            'user_id' => $request->user()->id,
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'total_price' => $totalPrice,
            'status' => 'pending',
        ]);

        return response()->json($order, 201);
    }

    /**
     * Display the specified resource.
     * @param Request $request
     * @param Order $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, Order $order)
    {
        $this->checkOwnership($request->user(), $order);

        return response()->json($order);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param Order $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Order $order)
    {
        $this->checkOwnership($request->user(), $order);

        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $product = $order->product;
        $totalPrice = $product->price * $request->quantity;

        $order->update([
            'quantity' => $request->quantity,
            'total_price' => $totalPrice,
        ]);

        return response()->json($order);
    }

    /**
     * Remove the specified resource from storage.
     * @param Request $request
     * @param Order $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, Order $order)
    {
        $this->checkOwnership($request->user(), $order);

        $order->delete();

        return response()->json(null, 204);
    }
}