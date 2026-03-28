<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/about', function () {
    return view('public.about');
});

Route::get('/contact', function () {
    return view('public.contact');
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.request');

Route::get('/reset-password', function (\Illuminate\Http\Request $request) {
    return view('auth.reset-password', ['token' => $request->token, 'email' => $request->email]);
})->name('password.reset');

Route::get('/seller/dashboard', function () {
    return view('seller.dashboard');
})->name('seller.dashboard');

Route::get('/customer/dashboard', function () {
    return view('customer.dashboard');
})->name('customer.dashboard');

Route::get('/stores', function () {
    return view('public.stores');
});

Route::get('/stores/{slug}', function ($slug) {
    return view('public.store-details', ['slug' => $slug]);
});

Route::get('/products', function () {
    return view('public.products');
});

Route::get('/products/{slug}', function ($slug) {
    return view('public.product-details', ['slug' => $slug]);
});

Route::get('/cart', function () {
    return view('public.cart');
});

Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
});

Route::get('lang/{locale}', function ($locale) {
    if (in_array($locale, ['ar', 'en'])) {
        session()->put('locale', $locale);
    }
    return redirect()->back();
});

Route::get('/create-admin', function () {
    $user = \App\Models\User::firstOrCreate(
    ['email' => 'admin@manzili.local'],
    [
        'name' => 'Admin Manzili',
        'password' => \Illuminate\Support\Facades\Hash::make('password'),
        'role' => 'admin',
        'email_verified_at' => now(),
        'otp_code' => null
    ]
    );
    return '✅ تم إنشاء حساب الإدارة بنجاح! <br> الإيميل: <b>admin@manzili.local</b> <br> كلمة المرور: <b>password</b>';
});

Route::get('/test-gemini', function () {
    $apiKey = env('GEMINI_API_KEY');
    if (!$apiKey)
        return "No GEMINI_API_KEY found.";
    $response = \Illuminate\Support\Facades\Http::withoutVerifying()->get("https://generativelanguage.googleapis.com/v1beta/models?key={$apiKey}");
    return response()->json($response->json());
});
