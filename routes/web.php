<?php

use App\Models\ProductVariant;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

Route::livewire('/', 'public.home-pearls')
    ->name('home-pearls');

Route::livewire('/categories/{category}', 'public.category-products')
    ->name('categories.show');

Route::livewire('/products/{product}', 'public.product-show')
    ->name('products.show');

Route::livewire('/products', 'public.products')
    ->name('products');

Route::livewire('/order/success', 'public.order-success')
    ->name('order.success');

Route::livewire('cart', 'public.cart')
    ->name('cart');
Route::livewire('checkout', 'public.check-out')
    ->name('checkout');


Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth'])
    ->group(function () {

        // Dashboard
        Route::livewire('/', 'admin.dashboard')->name('dashboard');

        // Core management (accessible to admins/employees too)
        Route::livewire('/categories', 'admin.categories')->name('categories');
        Route::livewire('/products', 'admin.products')->name('products');
        Route::livewire('/product-variants', 'admin.product-variants')->name('product-variants');
        Route::livewire('/orders', 'admin.orders')->name('orders');

        // 🔒 SUPER ADMIN ONLY
        Route::middleware(['role:super admin'])->group(function () {
        Route::livewire('/users', 'admin.users')->name('users');
        });

    }); 


Route::get('/products/image/{product}', function (Product $product) {
    abort_unless($product->image_url, 404);

    return redirect()->away($product->image_url);
})->name('products.image');

Route::get('/product-variants/image/{variant}', function (ProductVariant $variant) {
    abort_unless($variant->image_url, 404);

    return redirect()->away($variant->image_url);
})->name('product-variants.image');

require __DIR__.'/settings.php';
