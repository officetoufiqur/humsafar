<?php

namespace App\Http\Controllers;

use App\Helpers\FileUpload;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\Seo;
use App\Trait\ApiResponse;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    use ApiResponse;

    public function category(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $slug = str_replace(' ', '-', strtolower($request->name));

        $category = new BlogCategory();
        $category->name = $request->name;
        $category->slug = $slug;
        $category->status = true;
        $category->save();

        return $this->successResponse($category, 'Category created successfully');
    }

    public function getBlogs()
    {
        $blog = Blog::with('seo')->get();
        return $this->successResponse($blog, 'Blogs fetched successfully');
    }

    public function blogs(Request $request)
    {
        $request->validate([
           'title' => 'required|string|max:255',
           'category' => 'required|string|max:255',
           'slug' => 'required|string|max:255',
           'description' => 'required|string',
           'short_description' => 'required|string',
           'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,svg|max:2048',
           'meta_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,svg|max:2048',
        ]);

        $metaFile = null;
        if ($request->hasFile('meta_image')) {
            $metaFile = FileUpload::storeFile($request->file('meta_image'), 'uploads/seos');
        }

        $seo = Seo::create([
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $request->meta_keywords,
            'meta_image' => $metaFile
        ]);
        
        $file = null;
        if ($request->hasFile('image')) {
            $file = FileUpload::storeFile($request->file('image'), 'uploads/blogs');
        }

        $blog = new Blog();
        $blog->seo_id = $seo->id;
        $blog->title = $request->title;
        $blog->category = $request->category;
        $blog->slug = $request->slug;
        $blog->description = $request->description;
        $blog->short_description = $request->short_description;
        $blog->image = $file;
        $blog->status = true;
        $blog->save();

        return $this->successResponse($blog, 'Blog created successfully');
    }
}
