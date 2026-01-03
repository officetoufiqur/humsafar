<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = [
        'seo_id',
        'category_id',
        'title',
        'category',
        'slug',
        'description',
        'short_description',
        'image',
        'status'
    ];

    public function seo()
    {
        return $this->belongsTo(Seo::class);
    }

    public function category()
    {
       return $this->belongsTo(BlogCategory::class, 'category_id');
    }
}
