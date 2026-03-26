<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'store_id', 'category_id', 'name', 'slug', 'description', 
        'price', 'images', 'status', 'views'
    ];
    protected $casts = ['images' => 'array'];

    public function store() { return $this->belongsTo(Store::class); }
    public function category() { return $this->belongsTo(Category::class); }
    public function favorites() { return $this->hasMany(Favorite::class); }
    public function salesLogs() { return $this->hasMany(SalesLog::class); }
    public function reviews() { return $this->hasMany(ProductReview::class); }
}
