<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'quantity'];

    protected $hidden = ['created_at', 'updated_at'];

    protected $with = ['product'];

    public function product()
    {
        return $this->belongsTo(Product::class)
            ->without('inventory');
    }
}
