<?php
namespace App\Http\Controllers\API\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'seller') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $store = $user->stores()->first();
        if (!$store) {
            return response()->json(['error' => 'Store not found'], 404);
        }

        $products = Product::where('store_id', $store->id)->latest()->paginate(20);
        return response()->json($products);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $store = $user->stores()->first();

        if ($store->kyc_status !== 'approved') {
            return response()->json(['error' => 'لا يمكنك إضافة منتجات قبل الموافقة على توثيق المتجر'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:active,hidden,available,unavailable,deleted',
            'category_id' => 'nullable|exists:categories,id',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $validated['store_id'] = $store->id;
        $validated['slug'] = Str::slug($validated['name'] . '-' . uniqid());

        $imageUrls = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('products', 'public');
                $imageUrls[] = '/storage/' . $path;
            }
        }
        $validated['images'] = $imageUrls;

        $product = Product::create($validated);

        $admins = \App\Models\User::where('role', 'admin')->get();
        if ($admins->count() > 0) {
            \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\AdminAlert(
                'منتج جديد',
                'قام متجر (' . $store->name . ') بإضافة منتج جديد: ' . $product->name,
                'info',
                '/admin/dashboard'
                ));
        }

        return response()->json(['message' => 'Product created successfully', 'product' => $product], 201);
    }

    public function show(Request $request, $id)
    {
        $store = $request->user()->stores()->first();
        $product = Product::where('store_id', $store->id)->findOrFail($id);

        return response()->json($product);
    }

    public function update(Request $request, $id)
    {
        $store = $request->user()->stores()->first();
        $product = Product::where('store_id', $store->id)->findOrFail($id);

        if ($store->kyc_status !== 'approved') {
            return response()->json(['error' => 'المتجر غير موثق حالياً'], 403);
        }

        if ($product->status === 'deleted_by_admin') {
            return response()->json(['error' => 'لا يمكنك تعديل هذا المنتج، لقد تم حذفه من قبل الإدارة'], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
            'status' => 'sometimes|required|in:active,hidden,available,unavailable,deleted',
            'category_id' => 'nullable|exists:categories,id',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('images')) {
            $imageUrls = [];
            foreach ($request->file('images') as $file) {
                $path = $file->store('products', 'public');
                $imageUrls[] = '/storage/' . $path;
            }
            $validated['images'] = $imageUrls;
        }

        $product->update($validated);

        $admins = \App\Models\User::where('role', 'admin')->get();
        if ($admins->count() > 0) {
            \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\AdminAlert(
                'تحديث منتج',
                'قام متجر (' . $store->name . ') بتحديث منتج: ' . $product->name,
                'info',
                '/admin/dashboard'
                ));
        }

        return response()->json(['message' => 'Product updated successfully', 'product' => $product]);
    }

    public function destroy(Request $request, $id)
    {
        $store = $request->user()->stores()->first();
        $product = Product::where('store_id', $store->id)->findOrFail($id);

        if ($product->status === 'deleted_by_admin') {
            return response()->json(['error' => 'هذا المنتج محذوف من قبل الإدارة ولا يمكن إجراء تعديلات عليه'], 403);
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }
}
