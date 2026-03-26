<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $fillable = [
        'user_id', 'name', 'store_name', 'owner_name', 'store_type', 'business_type', 'slug', 'description',
        'logo', 'cover', 'cover_image', 'contact_info', 'kyc_status', 'status', 'views', 'verified_at',
        'whatsapp_number', 'location', 'product_types', 'identity_image', 'store_activity',
        'latitude', 'longitude', 'location_description', 'identity_front', 'identity_back'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function products()
    {
        return $this->hasMany(Product::class);
    }
    public function followers()
    {
        return $this->hasMany(Follower::class);
    }
    public function salesLogs()
    {
        return $this->hasMany(SalesLog::class);
    }
    public function reviews()
    {
        return $this->hasMany(StoreReview::class);
    }
    public function ads()
    {
        return $this->hasMany(Ad::class);
    }
}
