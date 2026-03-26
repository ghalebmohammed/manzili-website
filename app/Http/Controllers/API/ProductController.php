<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('store')
            ->where('status', 'active')
            ->withAvg('reviews', 'rating')
            ->withCount('reviews');

        if ($request->has('q') && $request->q !== '') {
            $query->where('name', 'like', '%' . $request->q . '%');
        }

        if ($request->has('category') && $request->category !== '') {
            $query->whereHas('category', function ($q) use ($request) {
                // Check if searching by slug, or name
                $q->where('slug', $request->category)
                    ->orWhere('name_ar', $request->category); // Since user passes arabic names directly from text
            });
        }

        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'price_low':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_high':
                    $query->orderBy('price', 'desc');
                    break;
                case 'highest_rated':
                    $query->orderBy('reviews_avg_rating', 'desc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }
        }
        else {
            $query->orderBy('created_at', 'desc');
        }

        $paginator = $query->paginate(20);

        if ($user = auth('sanctum')->user()) {
            $favorites = $user->favorites()->pluck('product_id')->toArray();
            $paginator->getCollection()->transform(function ($product) use ($favorites) {
                $product->is_favorited = in_array($product->id, $favorites);
                return $product;
            });
        }

        return response()->json($paginator);
    }
    public function show($slug)
    {
        $product = Product::with(['store', 'category'])
            ->where('slug', $slug)
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->firstOrFail();

        if ($user = auth('sanctum')->user()) {
            $product->is_favorited = $user->favorites()->where('product_id', $product->id)->exists();
        }
        else {
            $product->is_favorited = false;
        }

        // Increase view count when the product details page is visited
        $product->increment('views');

        return response()->json($product);
    }

    public function featured()
    {
        $featuredProductIds = \App\Models\FeaturedItem::where('item_type', 'product')
            ->where('end_at', '>', now())
            ->pluck('item_id');

        $products = Product::whereIn('id', $featuredProductIds)
            ->where('status', 'active')
            ->with(['store', 'category'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->get();

        return response()->json(['data' => $products]);
    }
}
