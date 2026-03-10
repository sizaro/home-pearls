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


require __DIR__.'/settings.php';
