<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
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
        return $request->user()->orders()->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $orderItems = collect($request->items)
            ->map(function ($item) {
                return new OrderItem($item);
            })
            ->filter(function ($item) {
                return $item->product->inventory->count >= $item->quantity;
            });

        if ($orderItems->count() < count($request->items)) {
            return response(['message' => 'error'], 422);
        }

        $orderItems->each(function ($item) {
            $item->product->inventory()->decrement('count', $item->quantity);
        });

        $order = Order::create(['user_id' => $request->user_id]);
        $order->items()->saveMany($orderItems);

        return $order->fresh();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        $this->authorize('update-order', $order);

        return $order;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        $this->authorize('update-order', $order);

        $order->items()->delete();
        $order->items()->createMany($request->items);

        return $order->fresh();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        $this->authorize('update-order', $order);

        $order->items()->delete();
        $order->delete();

        return response([], 200);
    }
}
