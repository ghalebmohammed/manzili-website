<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\StoreController;
use App\Http\Controllers\API\ReviewController;
use App\Http\Controllers\API\FavoriteController;
use App\Http\Controllers\API\FollowerController;
use App\Http\Controllers\API\SaleController;
use App\Http\Controllers\API\AssistantController;
use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\NotificationController;

use App\Http\Controllers\API\Customer\DashboardController as CustomerDashboardController;

use App\Http\Controllers\API\Seller\DashboardController as SellerDashboardController;
use App\Http\Controllers\API\Seller\StoreController as SellerStoreController;
use App\Http\Controllers\API\Seller\ProductController as SellerProductController;
use App\Http\Controllers\API\Seller\SaleController as SellerSaleController;

// Public routes
Route::post('/register', [AuthController::class , 'register']);
Route::post('/verify-email', [AuthController::class , 'verifyEmail']);
Route::post('/resend-verify-email', [AuthController::class , 'resendVerificationCode']);
Route::post('/login', [AuthController::class , 'login']);
Route::post('/quick-login', [AuthController::class , 'quickLogin']);
Route::post('/forgot-password', [AuthController::class , 'forgotPassword']);
Route::post('/reset-password', [AuthController::class , 'resetPassword']);

Route::get('/products', [ProductController::class , 'index']);
Route::get('/products/{slug}', [ProductController::class , 'show']);
Route::get('/products/{slug}/reviews', [ReviewController::class , 'productReviews']);

Route::get('/stores', [StoreController::class , 'index']);
Route::get('/stores/{slug}', [StoreController::class , 'show']);
Route::get('/stores/{slug}/products', [StoreController::class , 'products']);
Route::get('/stores/{slug}/reviews', [ReviewController::class , 'storeReviews']);

Route::get('/featured/stores', [StoreController::class , 'featured']);
Route::get('/featured/products', [ProductController::class , 'featured']);

Route::get('/categories', function () {
    return \App\Models\Category::all();
});

Route::post('/assistant/chat', [AssistantController::class , 'chat']);
Route::get('/assistant/analyze-store/{slug}', [AssistantController::class , 'analyzeStore']);
Route::get('/assistant/analyze-product/{slug}', [AssistantController::class , 'analyzeProduct']);
// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class , 'logout']);
    Route::get('/user', function (Request $request) {
            return $request->user();
        }
        );

        // Notifications
        Route::get('/notifications', [NotificationController::class , 'index']);
        Route::put('/notifications/{id}/read', [NotificationController::class , 'markAsRead']);
        Route::put('/notifications/read-all', [NotificationController::class , 'markAllAsRead']);

        // Reviews
        Route::post('/products/{id}/reviews', [ReviewController::class , 'storeProductReview']);
        Route::post('/stores/{id}/reviews', [ReviewController::class , 'storeStoreReview']);

        // Favorites & Followers
        Route::get('/favorites/products', [FavoriteController::class , 'index']);
        Route::post('/favorites/products/{id}', [FavoriteController::class , 'toggleFavorite']);

        Route::get('/followers/stores', [FollowerController::class , 'index']);
        Route::post('/followers/stores/{id}', [FollowerController::class , 'toggleFollow']);

        // Customer
        Route::get('/customer/orders', [CustomerDashboardController::class , 'orders']);
        Route::post('/sales/initiate', [SaleController::class , 'initiate']);

        // Seller routes
        Route::prefix('seller')->group(function () {
            Route::get('/dashboard/stats', [SellerDashboardController::class , 'stats']);

            Route::get('/store', [SellerStoreController::class , 'show']);
            Route::post('/store/profile', [SellerStoreController::class , 'updateProfile']);
            Route::post('/store/kyc', [SellerStoreController::class , 'uploadKyc']);
            Route::get('/kyc-status', [SellerStoreController::class , 'kycStatus']);

            Route::apiResource('/products', SellerProductController::class);

            Route::get('/sales', [SellerSaleController::class , 'index']);
            Route::put('/sales/{id}/status', [SellerSaleController::class , 'updateStatus']);
        }
        );

        // Admin routes
        Route::prefix('admin')->group(function () {
            Route::get('/dashboard/stats', [AdminController::class , 'stats']);
            Route::get('/stores', [AdminController::class , 'stores']);
            Route::put('/stores/{id}/kyc', [AdminController::class , 'updateStoreKyc']);
            Route::put('/stores/{id}/toggle-status', [AdminController::class , 'toggleStoreStatus']);
            Route::delete('/stores/{id}', [AdminController::class , 'deleteStore']);
            Route::get('/stores/{id}/products', [AdminController::class , 'storeProducts']);
            Route::delete('/products/{id}', [AdminController::class , 'deleteProduct']);
            Route::put('/products/{id}/approve', [AdminController::class , 'approveProduct']);
            Route::post('/notifications/send', [AdminController::class , 'sendGlobalNotification']);

            Route::get('/featured', [AdminController::class , 'getFeaturedItems']);
            Route::post('/featured', [AdminController::class , 'addFeaturedItem']);
            Route::put('/featured/{id}/renew', [AdminController::class , 'renewFeaturedItem']);
            Route::delete('/featured/{id}', [AdminController::class , 'removeFeaturedItem']);
        }
        );
    });
