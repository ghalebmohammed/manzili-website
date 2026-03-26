<?php
$dir = __DIR__ . '/app/Http/Controllers/API';
if (!is_dir($dir))
    mkdir($dir, 0755, true);

$controllers = [
    'AuthController' => "<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request \$request)
    {
        \$request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:customer,seller',
            'phone' => 'nullable|string|max:20',
        ]);
        \$user = User::create([
            'name' => \$request->name,
            'email' => \$request->email,
            'password' => Hash::make(\$request->password),
            'role' => \$request->role,
            'phone' => \$request->phone,
        ]);
        // default store if seller
        if (\$request->role === 'seller') {
            \$user->stores()->create([
                'name' => \$user->name . ' Store',
                'slug' => Str::slug(\$user->name . '-' . uniqid()),
            ]);
        }
        \$token = \$user->createToken('auth_token')->plainTextToken;
        return response()->json(['access_token' => \$token, 'token_type' => 'Bearer', 'user' => \$user], 201);
    }
    public function login(Request \$request)
    {
        \$request->validate(['email' => 'required|email', 'password' => 'required']);
        \$user = User::where('email', \$request->email)->first();
        if (!\$user || !Hash::check(\$request->password, \$user->password)) {
            throw ValidationException::withMessages(['email' => ['بيانات الدخول غير صحيحة.']]);
        }
        \$token = \$user->createToken('auth_token')->plainTextToken;
        return response()->json(['access_token' => \$token, 'token_type' => 'Bearer', 'user' => \$user]);
    }
    public function logout(Request \$request)
    {
        \$request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'تم تسجيل الخروج بنجاح']);
    }
}
",
    'StoreController' => "<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index(Request \$request)
    {
        \$query = Store::where('status', 'active');
        if (\$request->has('q')) {
            \$query->where('name', 'like', '%' . \$request->q . '%');
        }
        return response()->json(\$query->paginate(15));
    }
    public function show(\$slug)
    {
        \$store = Store::where('slug', \$slug)->withCount(['products', 'followers', 'reviews'])->withAvg('reviews', 'rating')->firstOrFail();
        return response()->json(\$store);
    }
    public function products(\$slug)
    {
        \$store = Store::where('slug', \$slug)->firstOrFail();
        return response()->json(\$store->products()->where('status', 'active')->paginate(15));
    }
}
",
    'ProductController' => "<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request \$request)
    {
        \$query = Product::with('store')->where('status', 'active');
        if (\$request->has('q')) {
            \$query->where('name', 'like', '%' . \$request->q . '%');
        }
        return response()->json(\$query->paginate(20));
    }
    public function show(\$slug)
    {
        \$product = Product::with(['store', 'category'])->where('slug', \$slug)->withAvg('reviews', 'rating')->withCount('reviews')->firstOrFail();
        return response()->json(\$product);
    }
}
",
    'ReviewController' => "<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function productReviews(\$slug)
    {
        \$product = Product::where('slug', \$slug)->firstOrFail();
        return response()->json(\$product->reviews()->with('user')->latest()->paginate(10));
    }
    public function storeReviews(\$slug)
    {
        \$store = Store::where('slug', \$slug)->firstOrFail();
        return response()->json(\$store->reviews()->with('user')->latest()->paginate(10));
    }
    public function storeProductReview(Request \$request, \$id)
    {
        \$request->validate(['rating' => 'required|integer|min:1|max:5', 'comment' => 'nullable|string']);
        \$product = Product::findOrFail(\$id);
        \$review = \$product->reviews()->create([
            'user_id' => \$request->user()->id,
            'rating' => \$request->rating,
            'comment' => \$request->comment
        ]);
        return response()->json(\$review, 201);
    }
    public function storeStoreReview(Request \$request, \$id)
    {
        \$request->validate(['rating' => 'required|integer|min:1|max:5', 'comment' => 'nullable|string']);
        \$store = Store::findOrFail(\$id);
        \$review = \$store->reviews()->create([
            'user_id' => \$request->user()->id,
            'rating' => \$request->rating,
            'comment' => \$request->comment
        ]);
        return response()->json(\$review, 201);
    }
}
",
    'SaleController' => "<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\SalesLog;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function initiate(Request \$request)
    {
        \$request->validate([
            'product_id' => 'required|exists:products,id',
            'customer_contact' => 'nullable|string'
        ]);
        \$product = Product::findOrFail(\$request->product_id);
        \$sale = SalesLog::create([
            'store_id' => \$product->store_id,
            'product_id' => \$product->id,
            'customer_id' => \$request->user() ? \$request->user()->id : null,
            'customer_contact' => \$request->customer_contact,
            'status' => 'pending'
        ]);
        return response()->json(['message' => 'Sale initiated successfully', 'sale' => \$sale], 201);
    }
}
"
];

foreach ($controllers as $name => $content) {
    file_put_contents($dir . '/' . $name . '.php', $content);
}

$routesFile = __DIR__ . '/routes/api.php';
$content = file_get_contents($routesFile);
$content = str_replace([
    "// Route::post('/register'",
    "// Route::post('/login'",
    "// Route::post('/forgot-password'",
    "// Route::post('/reset-password'",
    "// Route::get('/stores'",
    "// Route::get('/stores/{slug}'",
    "// Route::get('/stores/{slug}/products'",
    "// Route::get('/stores/{slug}/reviews'",
    "// Route::get('/products'",
    "// Route::get('/products/{slug}'",
    "// Route::get('/products/{slug}/reviews'",
    "// Route::post('/sales/initiate'",
    "// Route::post('/products/{id}/reviews'",
    "// Route::post('/stores/{id}/reviews'"
], [
    "Route::post('/register'",
    "Route::post('/login'",
    "Route::post('/forgot-password'",
    "Route::post('/reset-password'",
    "Route::get('/stores'",
    "Route::get('/stores/{slug}'",
    "Route::get('/stores/{slug}/products'",
    "Route::get('/stores/{slug}/reviews'",
    "Route::get('/products'",
    "Route::get('/products/{slug}'",
    "Route::get('/products/{slug}/reviews'",
    "Route::post('/sales/initiate'",
    "Route::post('/products/{id}/reviews'",
    "Route::post('/stores/{id}/reviews'"
], $content);
file_put_contents($routesFile, $content);

echo "Controllers and Routes generated successfully.\n";
