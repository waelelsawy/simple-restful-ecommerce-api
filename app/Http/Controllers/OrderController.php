<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Rules\InStock;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * The validation rules.
     *
     * @var array
     */
    protected $rules;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->rules = [
            'items' => 'required|array',
            'items.*.product_id' => 'required|integer|min:1|exists:products,id',
            'items.*.quantity' => ['required', 'integer', 'min:1', new InStock]
        ];
    }

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
        $validated = $request->validate($this->rules);

        $order = $request->user()->orders()->create();
        $order->items()->createMany($validated['items']);

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

        $validated = $request->validate($this->rules);

        $order->items->each->delete();
        $order->items()->createMany($validated['items']);

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

        $order->items->each->delete();
        $order->delete();

        return response([], 200);
    }
}
