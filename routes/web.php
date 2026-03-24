<?php

use Illuminate\Support\Facades\Route;

Route::livewire('/', 'public.home-pearls')
    ->name('home-pearls');

Route::livewire('/categories/{category}', 'public.category-products')
    ->name('categories.show');

Route::livewire('/products/{product}', 'public.product-show')
    ->name('products.show');

Route::livewire('/products', 'public.products')
    ->name('products');



Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:super admin'])
    ->group(function () {

        Route::livewire('/', 'admin.dashboard')->name('dashboard');

        Route::livewire('/categories', 'admin.categories')->name('categories');
        Route::livewire('/products', 'admin.products')->name('products');
        Route::livewire('/product-variants', 'admin.product-variants')->name('product-variants');

    });    


require __DIR__.'/settings.php';
