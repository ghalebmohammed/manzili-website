<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index(Request $request)
    {
        $query = Store::where('status', 'active')
            ->withAvg('reviews', 'rating');

        if ($request->has('type')) {
            $type = $request->type;
            if ($type === 'merchants') {
                $query->where('business_type', 'تاجر');
            }
            elseif ($type === 'home_projects') {
                $query->where('business_type', 'مشروع منزلي');
            }
        }

        if ($request->has('q')) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }

        $sort = $request->get('sort', 'newest');
        if ($sort === 'rating') {
            $query->orderBy('reviews_avg_rating', 'desc');
        }
        elseif ($sort === 'oldest') {
            $query->orderBy('created_at', 'asc');
        }
        else {
            $query->orderBy('created_at', 'desc');
        }

        return response()->json($query->paginate(15));
    }
    public function show($slug)
    {
        $store = Store::where('slug', $slug)
            ->with(['user'])
            ->withCount(['products', 'followers', 'reviews'])
            ->withAvg('reviews', 'rating')
            ->firstOrFail();

        // Increase view count when the store profile is visited
        $store->increment('views');

        $data = $store->toArray();
        if (auth('sanctum')->check()) {
            $user = auth('sanctum')->user();
            if ($user->role === 'customer') {
                $data['is_followed'] = $user->followers()->where('store_id', $store->id)->exists();
            }
        }

        return response()->json($data);
    }
    public function products($slug)
    {
        $store = Store::where('slug', $slug)->firstOrFail();
        return response()->json(
            $store->products()
            ->where('status', 'active')
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->paginate(15)
        );
    }

    public function featured()
    {
        $featuredStoreIds = \App\Models\FeaturedItem::where('item_type', 'store')
            ->where('end_at', '>', now())
            ->pluck('item_id');

        $stores = Store::whereIn('id', $featuredStoreIds)
            ->where('status', 'active')
            ->withAvg('reviews', 'rating')
            ->get();

        return response()->json(['data' => $stores]);
    }
}
