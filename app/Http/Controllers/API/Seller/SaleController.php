<?php
namespace App\Http\Controllers\API\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SalesLog;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $store = $user->stores()->first();

        $sales = SalesLog::where('store_id', $store->id)
            ->with(['product', 'customer'])
            ->latest()
            ->paginate(20);

        return response()->json($sales);
    }

    public function updateStatus(Request $request, $id)
    {
        $user = $request->user();
        $store = $user->stores()->first();

        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled'
        ]);

        $sale = SalesLog::where('store_id', $store->id)->findOrFail($id);
        $sale->update(['status' => $validated['status']]);

        return response()->json(['message' => 'تم تحديث حالة الطلب بنجاح', 'sale' => $sale]);
    }
}
