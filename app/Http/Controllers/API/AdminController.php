<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Store;
use App\Models\Product;
use App\Models\SalesLog;
use App\Notifications\KycStatusNotification;
use App\Notifications\GeneralNotification;

class AdminController extends Controller
{
    public function stats(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $usersCount = User::count();
        $storesCount = Store::count();
        $productsCount = Product::count();
        $pendingKyc = Store::where('kyc_status', 'pending')->whereNotNull('identity_front')->count();
        $totalSales = SalesLog::where('status', 'confirmed')->count();

        return response()->json([
            'users' => $usersCount,
            'stores' => $storesCount,
            'products' => $productsCount,
            'pending_kyc_count' => $pendingKyc,
            'total_sales' => $totalSales
        ]);
    }

    public function stores(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $stores = Store::with('user')->get();
        return response()->json(['data' => $stores]);
    }

    public function updateStoreKyc(Request $request, $id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:approved,rejected,pending'
        ]);

        $store = Store::with('user')->findOrFail($id);
        $store->kyc_status = $validated['status'];
        if ($validated['status'] === 'approved') {
            $store->status = 'active';
        }
        else if ($validated['status'] === 'rejected') {
            $store->status = 'inactive';
        }
        $store->save();

        if ($store->user) {
            $store->user->notify(new KycStatusNotification($store->kyc_status, $store->name));
        }

        return response()->json(['message' => 'تم تحديث حالة المتجر بنجاح']);
    }

    public function sendGlobalNotification(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'target_role' => 'nullable|in:customer,seller,all',
            'url' => 'nullable|url'
        ]);

        $query = User::query();
        if (!empty($validated['target_role']) && $validated['target_role'] !== 'all') {
            $query->where('role', $validated['target_role']);
        }

        $users = $query->get();
        foreach ($users as $user) {
            $user->notify(new GeneralNotification($validated['title'], $validated['message'], $validated['url']));
        }

        return response()->json(['message' => 'تم إرسال الإشعار لـ ' . $users->count() . ' مستخدمين بنجاح.']);
    }

    // New Store Management
    public function toggleStoreStatus(Request $request, $id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $store = Store::findOrFail($id);
        $store->status = $store->status === 'active' ? 'suspended' : 'active';
        $store->save();

        if ($store->status === 'suspended' && $store->user) {
            $message = "مرحباً " . $store->name . " نبلغكم انه تم ايقاف المتجر لأسباب مخالفات او إجراءات أخرى الرجاء التواصل مع الدعم الفني لتفادي الإشكاليه 775552127";
            $store->user->notify(new GeneralNotification('إيقاف المتجر', $message, null));
        }
        elseif ($store->status === 'active' && $store->user) {
            $message = "تم إعادة تفعيل الحساب بنجاح شكراً لتواصلكم وتفادي الإشكالية";
            $store->user->notify(new GeneralNotification('تفعيل المتجر', $message, null));
        }

        return response()->json(['message' => 'تم تحديث حالة المتجر', 'status' => $store->status]);
    }
    public function deleteStore(Request $request, $id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $store = Store::findOrFail($id);

        $userName = "";
        $userToNotify = null;
        if ($store->user) {
            $userName = $store->user->name;
            $userToNotify = $store->user;
        }

        // Delete the store. Due to cascading setup, products might be deleted, check later.
        $store->delete();

        if ($userToNotify) {
            $message = "مرحباً، نبلغكم بأنه تم إغلاق وحذف متجركم نهائياً لمخالفة سياسات المنصة. للشكاوى، يرجى التواصل مع الدعم الفني.";
            $userToNotify->notify(new GeneralNotification('حذف المتجر', $message, null));
        }

        return response()->json(['message' => 'تم حذف المتجر نهائياً بنجاح']);
    }

    public function storeProducts(Request $request, $id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $products = Product::where('store_id', $id)->get();
        return response()->json(['data' => $products]);
    }

    public function deleteProduct(Request $request, $id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $product = Product::with('store.user')->findOrFail($id);

        $product->status = 'deleted_by_admin';
        $product->save();

        if ($product->store && $product->store->user) {
            $productUrl = url('/products/' . $product->slug);
            $message = "مرحباً " . $product->store->name . " نبلغكم انه تم حذف المنتج التالي $productUrl لأسباب مخالفات او إجراءات أخرى الرجاء التواصل مع الدعم الفني لتفادي الإشكاليه 775552127";
            $product->store->user->notify(new GeneralNotification('إشعار بخصوص منتج', $message, $productUrl));
        }

        return response()->json(['message' => 'تم حذف المنتج']);
    }

    public function approveProduct(Request $request, $id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $product = Product::findOrFail($id);
        // Assuming we just set status to active when approved
        $product->status = 'active';
        $product->save();
        return response()->json(['message' => 'تم قبول المنتج للنشر']);
    }

    public function getFeaturedItems(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $items = \App\Models\FeaturedItem::with(['store', 'product', 'product.store'])
            ->orderBy('id', 'desc')
            ->get();

        // Map data to be easier for frontend
        $mappedItems = $items->map(function ($item) {
            if ($item->item_type === 'store') {
                $name = $item->store ? $item->store->name : 'متجر غير معروف';
            }
            else {
                $productName = $item->product ? $item->product->name : 'منتج غير معروف';
                $storeName = ($item->product && $item->product->store) ? $item->product->store->name : 'متجر غير معروف';
                $name = $productName . ' (متجر: ' . $storeName . ')';
            }

            return [
            'id' => $item->id,
            'item_type' => $item->item_type,
            'item_id' => $item->item_id,
            'name' => $name,
            'end_at' => $item->end_at->format('Y-m-d H:i:s'),
            'is_active' => $item->end_at > now()
            ];
        });

        return response()->json(['data' => $mappedItems]);
    }

    public function addFeaturedItem(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'item_type' => 'required|in:store,product',
            'item_id' => 'required|integer',
            'duration_hours' => 'required|integer|min:1'
        ]);

        $endAt = now()->addHours($validated['duration_hours']);

        $featured = \App\Models\FeaturedItem::create([
            'item_type' => $validated['item_type'],
            'item_id' => $validated['item_id'],
            'end_at' => $endAt
        ]);

        return response()->json([
            'message' => 'تم إضافة الإعلان بنجاح وسيظهر حتى ' . $endAt->format('Y-m-d H:i'),
            'data' => $featured
        ]);
    }

    public function renewFeaturedItem(Request $request, $id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'duration_hours' => 'required|integer|min:1'
        ]);

        $item = \App\Models\FeaturedItem::findOrFail($id);

        // If currently active, extend it. Else, create new timeframe.
        if ($item->end_at > now()) {
            $item->end_at = $item->end_at->addHours($validated['duration_hours']);
        }
        else {
            $item->end_at = now()->addHours($validated['duration_hours']);
        }

        $item->save();

        return response()->json([
            'message' => 'تم تجديد الإعلان بنجاح، سيستمر حتى ' . $item->end_at->format('Y-m-d H:i')
        ]);
    }

    public function removeFeaturedItem(Request $request, $id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $item = \App\Models\FeaturedItem::findOrFail($id);
        $item->delete();

        return response()->json(['message' => 'تم إزالة الإعلان بنجاح']);
    }
}
