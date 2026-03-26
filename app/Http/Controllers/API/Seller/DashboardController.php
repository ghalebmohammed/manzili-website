<?php
namespace App\Http\Controllers\API\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\SalesLog;

class DashboardController extends Controller
{
    public function stats(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'seller') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $store = $user->stores()->first();
        if (!$store) {
            return response()->json(['error' => 'Store not found'], 404);
        }

        $productsCount = Product::where('store_id', $store->id)->count();
        $pendingSales = SalesLog::where('store_id', $store->id)->where('status', 'pending')->count();
        $totalSales = SalesLog::where('store_id', $store->id)->where('status', 'confirmed')->count();
        $views = $store->views;

        return response()->json([
            'products_count' => $productsCount,
            'pending_sales' => $pendingSales,
            'total_sales' => $totalSales,
            'total_views' => $views,
            'store_status' => $store->status,
            'kyc_status' => $store->kyc_status,
        ]);
    }
}
