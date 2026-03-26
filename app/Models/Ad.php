<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    protected $fillable = ['store_id', 'banner_image', 'link', 'status', 'start_date', 'end_date'];
    protected $casts = ['start_date' => 'datetime', 'end_date' => 'datetime'];
    public function store() { return $this->belongsTo(Store::class); }
}
