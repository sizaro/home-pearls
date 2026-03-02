<?php

use Illuminate\Support\Facades\Route;

Route::livewire('/', 'public.home-pearls')->name('home-pearls');
Route::livewire('/products', 'public.products')->name('products');


require __DIR__.'/settings.php';
