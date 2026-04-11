<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariant;

class BackfillCreatedBySeeder extends Seeder
{
    public function run(): void
    {
        // Assign all existing categories
        Category::whereNull('created_by')
            ->update(['created_by' => 1]);

        // Assign all existing products
        Product::whereNull('created_by')
            ->update(['created_by' => 1]);

        // Assign all existing variants
        ProductVariant::whereNull('created_by')
            ->update(['created_by' => 1]);
    }
}
