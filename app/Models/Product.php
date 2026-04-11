<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'image_url',
         'created_by'
    ];

    /**
     * A product belongs to a category
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * A product has many variants
     */
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function creator()
{
    return $this->belongsTo(User::class, 'created_by');
}
}