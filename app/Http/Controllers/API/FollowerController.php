<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Store;

class FollowerController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'customer') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Get stores the user is following
        $following = $user->followers()->with('store')->latest()->paginate(20);

        return response()->json($following);
    }

    public function toggleFollow(Request $request, $id)
    {
        $user = $request->user();
        if ($user->role !== 'customer') {
            return response()->json(['error' => 'Only customers can follow stores'], 403);
        }

        $store = Store::findOrFail($id);

        // Check if already followed
        $existingFollow = $user->followers()->where('store_id', $store->id)->first();

        if ($existingFollow) {
            $existingFollow->delete();
            return response()->json(['message' => 'تم إلغاء متابعة المتجر', 'status' => 'unfollowed']);
        }
        else {
            $user->followers()->create(['store_id' => $store->id]);
            return response()->json(['message' => 'تمت متابعة المتجر بنجاح', 'status' => 'followed']);
        }
    }
}
