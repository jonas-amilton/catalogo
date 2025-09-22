<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'price', 'stock', 'active'];

    public function getRouteKeyName()
    {
        return 'id';
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}