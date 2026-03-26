<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SalesLog extends Model
{
    protected $fillable = ['store_id', 'product_id', 'customer_id', 'customer_contact', 'status'];
    public function store() { return $this->belongsTo(Store::class); }
    public function product() { return $this->belongsTo(Product::class); }
    public function customer() { return $this->belongsTo(User::class, 'customer_id'); }
}
