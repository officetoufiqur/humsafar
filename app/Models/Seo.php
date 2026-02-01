<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seo extends Model
{
    use HasFactory;

    protected $fillable = ['frontend_id','meta_title','meta_description','meta_keywords','meta_image','page_type','show_header'];

    public function frontend()
    {
        return $this->belongsTo(FrontendSetting::class, 'frontend_id');
    }
}
