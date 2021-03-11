<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['user_id'];

    protected $hidden = ['created_at', 'updated_at'];

    protected $with = ['items'];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
