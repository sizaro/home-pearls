<?php

use Illuminate\Support\Facades\Route;

Route::livewire('/', 'public.home-pearls')->name('home-pearls');
Route::livewire('/categories', 'public.products')->name('products');
Route::livewire('/products', 'public.products')->name('products');


require __DIR__.'/settings.php';
