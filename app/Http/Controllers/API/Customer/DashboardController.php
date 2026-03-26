<?php
namespace App\Http\Controllers\API\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SalesLog;

class DashboardController extends Controller
{
    public function orders(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'customer') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $orders = SalesLog::where('customer_id', $user->id)
            ->with(['store', 'product'])
            ->latest()
            ->paginate(20);

        return response()->json($orders);
    }
}
