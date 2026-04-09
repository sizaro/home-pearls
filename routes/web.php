<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
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
    ->name('order-success');



Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:super admin'])
    ->group(function () {

        Route::livewire('/', 'admin.dashboard')->name('dashboard');

        Route::livewire('/categories', 'admin.categories')->name('categories');
        Route::livewire('/products', 'admin.products')->name('products');
        Route::livewire('/product-variants', 'admin.product-variants')->name('product-variants');

    });    


    Route::get('/products/image/{id}', function ($id) {
    $product = Product::findOrFail($id);
    $path = 'private/products/' . $product->image_url;

    if (!Storage::exists($path)) {
        abort(404);
    }

    return response()->file(Storage::path($path));
})->name('products.image');



Route::get('/product-variants/image/{id}', function ($id) {
    $variant = ProductVariant::findOrFail($id);

    if (!$variant->image_url || !Storage::exists('private/product-variants/' . $variant->image_url)) {
        abort(404);
    }

    $file = Storage::get('private/product-variants/' . $variant->image_url);
    $type = Storage::mimeType('private/product-variants/' . $variant->image_url);

    return Response::make($file, 200, [
        'Content-Type' => $type,
        'Content-Disposition' => 'inline; filename="' . $variant->image_url . '"'
    ]);
})->name('product-variants.image');

require __DIR__.'/settings.php';
