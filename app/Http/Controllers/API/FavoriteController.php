<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'customer') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Get paginated favorite products for the user by mapping the pivot model
        $favorites = $user->favorites()->with('product.store')->latest()->paginate(20);
        $favorites->getCollection()->transform(function ($favorite) {
            return $favorite->product;
        });

        return response()->json($favorites);
    }

    public function toggleFavorite(Request $request, $id)
    {
        $user = $request->user();
        if ($user->role !== 'customer') {
            return response()->json(['error' => 'Only customers can favorite products'], 403);
        }

        $product = Product::findOrFail($id);

        // Check if already favorited
        if ($user->favorites()->where('product_id', $product->id)->exists()) {
            $user->favorites()->where('product_id', $product->id)->delete();
            return response()->json(['message' => 'تم إزالة المنتج من المفضلة', 'status' => 'removed']);
        }
        else {
            $user->favorites()->create(['product_id' => $product->id]);
            return response()->json(['message' => 'تم إضافة المنتج للمفضلة', 'status' => 'added']);
        }
    }
}
