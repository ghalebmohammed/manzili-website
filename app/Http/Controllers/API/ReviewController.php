<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function productReviews($slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();
        return response()->json($product->reviews()->with('user')->latest()->paginate(10));
    }
    public function storeReviews($slug)
    {
        $store = Store::where('slug', $slug)->firstOrFail();
        return response()->json($store->reviews()->with('user')->latest()->paginate(10));
    }
    public function storeProductReview(Request $request, $id)
    {
        $request->validate(['rating' => 'required|integer|min:1|max:5', 'comment' => 'nullable|string']);
        $product = Product::findOrFail($id);
        $review = $product->reviews()->create([
            'user_id' => $request->user()->id,
            'rating' => $request->rating,
            'comment' => $request->comment
        ]);
        return response()->json($review, 201);
    }
    public function storeStoreReview(Request $request, $id)
    {
        $request->validate(['rating' => 'required|integer|min:1|max:5', 'comment' => 'nullable|string']);
        $store = Store::findOrFail($id);
        $review = $store->reviews()->create([
            'user_id' => $request->user()->id,
            'rating' => $request->rating,
            'comment' => $request->comment
        ]);
        return response()->json($review, 201);
    }
}
