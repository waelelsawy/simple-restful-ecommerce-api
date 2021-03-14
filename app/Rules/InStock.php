<?php

namespace App\Rules;

use App\Models\Inventory;
use Illuminate\Contracts\Validation\Rule;

class InStock implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $otherAttribute = str_replace('quantity', 'product_id', $attribute);
        $productId = request()->input($otherAttribute);

        $order = request()->route('order');
        $item = $order?->items()->whereProductId($productId)->first();

        $inventory = Inventory::whereProductId($productId)->first();

        return $inventory?->count + $item?->quantity >= $value;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Invalid order, not enough inventory';
    }
}
