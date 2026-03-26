<?php
namespace App\Http\Controllers\API\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StoreController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'seller') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $store = $user->stores()->first();
        return response()->json($store);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'seller') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $store = $user->stores()->first();
        if (!$store) {
            return response()->json(['error' => 'Store not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'whatsapp_number' => 'required|string|max:20',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120'
        ]);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('store_logos', 'public');
            $validated['logo'] = '/storage/' . $path;
        }

        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('store_covers', 'public');
            $validated['cover_image'] = '/storage/' . $path;
        }

        $store->update($validated);

        return response()->json([
            'message' => 'تم حفظ بيانات الحساب بنجاح',
            'store' => $store
        ]);
    }

    public function uploadKyc(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'seller') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $store = $user->stores()->first();
        if (!$store) {
            return response()->json(['error' => 'Store not found'], 404);
        }

        $validated = $request->validate([
            'owner_name' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'business_type' => 'required|string|max:100',
            'store_activity' => 'required|string|max:100',
            'contact_info' => 'required|string|max:255',
            'location_description' => 'required|string|max:500',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'identity_front' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'identity_back' => 'nullable|image|mimes:jpeg,png,jpg|max:5120'
        ]);

        if ($request->hasFile('identity_front')) {
            $path1 = $request->file('identity_front')->store('kyc_documents', 'public');
            $validated['identity_front'] = '/storage/' . $path1;
        }

        if ($request->hasFile('identity_back')) {
            $path2 = $request->file('identity_back')->store('kyc_documents', 'public');
            $validated['identity_back'] = '/storage/' . $path2;
        }

        $validated['kyc_status'] = 'pending';

        $store->update($validated);

        $admins = \App\Models\User::where('role', 'admin')->get();
        if ($admins->count() > 0) {
            \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\AdminAlert(
                'طلب توثيق جديد',
                'قام متجر (' . $store->name . ') برفع وثائق التوثيق الخاصة به.',
                'warning',
                '/admin/dashboard'
                ));
        }

        return response()->json([
            'message' => 'تم إرسال طلب التوثيق بنجاح'
        ]);
    }

    public function kycStatus(Request $request)
    {
        $store = $request->user()->stores()->first();
        return response()->json(['status' => $store->kyc_status]);
    }
}
