<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['user_id'];

    protected $with = ['items'];

    // protected $casts = [
    //     'user_id' => 'int'
    // ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
