<?php

$modelsDir = __DIR__ . '/app/Models/';

$models = [
    'User' => "<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected \$fillable = [
        'name', 'email', 'phone', 'password', 'role',
    ];

    protected \$hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function stores() { return \$this->hasMany(Store::class); }
    public function favorites() { return \$this->hasMany(Favorite::class); }
    public function followers() { return \$this->hasMany(Follower::class); }
    public function productReviews() { return \$this->hasMany(ProductReview::class); }
    public function storeReviews() { return \$this->hasMany(StoreReview::class); }
    public function salesLogs() { return \$this->hasMany(SalesLog::class, 'customer_id'); }
}
",
    'Category' => "<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected \$fillable = ['name_ar', 'name_en', 'slug', 'icon', 'is_active'];
    public function products() { return \$this->hasMany(Product::class); }
}
",
    'Store' => "<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected \$fillable = [
        'user_id', 'name', 'store_name', 'store_type', 'slug', 'description', 
        'logo', 'cover', 'cover_image', 'contact_info', 'kyc_status', 'status', 'views', 'verified_at'
    ];

    public function user() { return \$this->belongsTo(User::class); }
    public function products() { return \$this->hasMany(Product::class); }
    public function followers() { return \$this->hasMany(Follower::class); }
    public function salesLogs() { return \$this->hasMany(SalesLog::class); }
    public function reviews() { return \$this->hasMany(StoreReview::class); }
    public function ads() { return \$this->hasMany(Ad::class); }
}
",
    'Product' => "<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected \$fillable = [
        'store_id', 'category_id', 'name', 'slug', 'description', 
        'price', 'images', 'status', 'views'
    ];
    protected \$casts = ['images' => 'array'];

    public function store() { return \$this->belongsTo(Store::class); }
    public function category() { return \$this->belongsTo(Category::class); }
    public function favorites() { return \$this->hasMany(Favorite::class); }
    public function salesLogs() { return \$this->hasMany(SalesLog::class); }
    public function reviews() { return \$this->hasMany(ProductReview::class); }
}
",
    'Favorite' => "<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    protected \$fillable = ['user_id', 'product_id'];
    public function user() { return \$this->belongsTo(User::class); }
    public function product() { return \$this->belongsTo(Product::class); }
}
",
    'Follower' => "<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Follower extends Model
{
    protected \$fillable = ['user_id', 'store_id'];
    public function user() { return \$this->belongsTo(User::class); }
    public function store() { return \$this->belongsTo(Store::class); }
}
",
    'SalesLog' => "<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SalesLog extends Model
{
    protected \$fillable = ['store_id', 'product_id', 'customer_id', 'customer_contact', 'status'];
    public function store() { return \$this->belongsTo(Store::class); }
    public function product() { return \$this->belongsTo(Product::class); }
    public function customer() { return \$this->belongsTo(User::class, 'customer_id'); }
}
",
    'ProductReview' => "<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ProductReview extends Model
{
    protected \$fillable = ['user_id', 'product_id', 'rating', 'comment'];
    public function user() { return \$this->belongsTo(User::class); }
    public function product() { return \$this->belongsTo(Product::class); }
}
",
    'StoreReview' => "<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class StoreReview extends Model
{
    protected \$fillable = ['user_id', 'store_id', 'rating', 'comment'];
    public function user() { return \$this->belongsTo(User::class); }
    public function store() { return \$this->belongsTo(Store::class); }
}
",
    'Ad' => "<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    protected \$fillable = ['store_id', 'banner_image', 'link', 'status', 'start_date', 'end_date'];
    protected \$casts = ['start_date' => 'datetime', 'end_date' => 'datetime'];
    public function store() { return \$this->belongsTo(Store::class); }
}
",
    'Setting' => "<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected \$fillable = ['key', 'value', 'type'];
}
",
    'Notification' => "<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected \$primaryKey = 'id';
    public \$incrementing = false;
    protected \$keyType = 'string';
    protected \$guarded = [];
}
"
];

foreach ($models as $name => $content) {
    file_put_contents($modelsDir . $name . '.php', $content);
    echo "Updated $name\n";
}
