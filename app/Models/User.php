<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'phone', 'password', 'role',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function stores() { return $this->hasMany(Store::class); }
    public function favorites() { return $this->hasMany(Favorite::class); }
    public function followers() { return $this->hasMany(Follower::class); }
    public function productReviews() { return $this->hasMany(ProductReview::class); }
    public function storeReviews() { return $this->hasMany(StoreReview::class); }
    public function salesLogs() { return $this->hasMany(SalesLog::class, 'customer_id'); }
}
