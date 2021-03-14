<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['product_id', 'quantity'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['created_at', 'updated_at'];

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['product'];

    public static function booted()
    {
        static::saved(function ($item) {
            $item->product->inventory()->decrement('count', $item->quantity);
        });

        static::deleting(function ($item) {
            $item->product->inventory()->increment('count', $item->quantity);
        });
    }

    /**
     * Get the product without it's inventory that owns the order item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class)
            ->without('inventory');
    }
}
