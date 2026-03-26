<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeaturedItem extends Model
{
    protected $fillable = ['item_type', 'item_id', 'end_at'];
    protected $casts = [
        'end_at' => 'datetime',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class , 'item_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class , 'item_id');
    }
}
