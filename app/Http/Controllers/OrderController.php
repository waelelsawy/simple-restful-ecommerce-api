<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return Order::whereUserId($request->user_id)->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        foreach ($request->items as $orderItem) {
            $product = Product::find($orderItem['product_id']);

            if ($product->inventory->count < $orderItem['quantity']) {
                return response(['message' => 'error'], 403);
            }
        }

        foreach ($request->items as $orderItem) {
            $product = Product::find($orderItem['product_id']);
            $product->inventory()->decrement('count', $orderItem['quantity']);
        }

        $order = Order::create(['user_id' => $request->user_id]);
        $order->items()->createMany($request->items);

        return response([], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $orderId
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, int $orderId)
    {
        $attributes = [
            'id' => $orderId,
            'user_id' => $request->user_id
        ];

        if ($order = Order::where($attributes)->first()) {
            return $order;
        }

        return response(['message' => 'Order not found'], 404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \int  $orderId
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $orderId)
    {
        $attributes = [
            'id' => $orderId,
            'user_id' => $request->user_id
        ];

        if ($order = Order::where($attributes)->first()) {
            $order->items()->delete();
            $order->items()->createMany($request->items);

            return response([], 200);
        }

        return response(['message' => 'Order not found'], 404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \int  $orderId
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, int $orderId)
    {
        $attributes = [
            'id' => $orderId,
            'user_id' => $request->user_id
        ];

        if ($order = Order::where($attributes)->first()) {
            $order->items()->delete();
            $order->delete();

            return response([], 200);
        }

        return response(['message' => 'Order not found'], 404);
    }
}
