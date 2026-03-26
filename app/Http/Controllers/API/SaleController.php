<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\SalesLog;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function initiate(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'customer_contact' => 'nullable|string'
        ]);
        $product = Product::findOrFail($request->product_id);
        $sale = SalesLog::create([
            'store_id' => $product->store_id,
            'product_id' => $product->id,
            'customer_id' => $request->user() ? $request->user()->id : null,
            'customer_contact' => $request->customer_contact,
            'status' => 'pending'
        ]);
        return response()->json(['message' => 'Sale initiated successfully', 'sale' => $sale], 201);
    }
}
