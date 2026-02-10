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

    public function gatCategory()
    {
        $categories = BlogCategory::withCount('blogs')->get();

        if (! $categories) {
            return $this->errorResponse('Category not found', 404);
        }

        return $this->successResponse($categories, 'Category fetched successfully');
    }

    public function category(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $slug = str_replace(' ', '-', strtolower($request->name));

        $category = new BlogCategory;
        $category->name = $request->name;
        $category->slug = $slug;
        $category->status = true;
        $category->save();

        return $this->successResponse($category, 'Category created successfully');
    }

    public function categoryEdit($id)
    {
        $category = BlogCategory::find($id);

        if (! $category) {
            return $this->errorResponse('Category not found', 404);
        }

        return $this->successResponse($category, 'Category fetched successfully');
    }

    public function categoryUpdate(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = BlogCategory::find($id);

        if (! $category) {
            return $this->errorResponse('Category not found', 404);
        }

        $slug = str_replace(' ', '-', strtolower($request->name));

        $category->name = $request->name;
        $category->slug = $slug;
        $category->save();

        return $this->successResponse($category, 'Category updated successfully');
    }

    public function categoryDelete($id)
    {
        $category = BlogCategory::find($id);

        if (! $category) {
            return $this->errorResponse('Category not found', 404);
        }

        $category->delete();

        return $this->successResponse(null, 'Category deleted successfully');
    }

    public function getBlogs()
    {
        $blog = Blog::with('seo')->latest()->get();

        return $this->successResponse($blog, 'Blogs fetched successfully');
    }

    public function statistics()
    {
        $blog = Blog::count();
        $active = Blog::where('status', true)->count();
        $inactive = Blog::where('status', false)->count();

        $data = [
            'total' => $blog,
            'active' => $active,
            'inactive' => $inactive,
        ];

        return $this->successResponse($data, 'Blogs fetched successfully');
    }

    public function blogs(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:blog_categories,id',
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
            'meta_image' => $metaFile,
        ]);

        $file = null;
        if ($request->hasFile('image')) {
            $file = FileUpload::storeFile($request->file('image'), 'uploads/blogs');
        }

        $category = BlogCategory::find($request->category_id);

        $blog = new Blog;
        $blog->seo_id = $seo->id;
        $blog->title = $request->title;
        $blog->category_id = $category->id;
        $blog->category = $category->name;
        $blog->slug = $request->slug;
        $blog->description = $request->description;
        $blog->short_description = $request->short_description;
        $blog->image = $file;
        $blog->status = true;
        $blog->save();

        return $this->successResponse($blog, 'Blog created successfully');
    }

    public function blogsEdit($id)
    {
        $blog = Blog::with('seo')->find($id);

        if (! $blog) {
            return $this->errorResponse('Blog not found', 404);
        }

        return $this->successResponse($blog, 'Blog fetched successfully');
    }

    public function blogsUpdate(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:blog_categories,id',
            'short_description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,svg|max:2048',
            'meta_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,svg|max:2048',
        ]);

        $blog = Blog::find($id);

        if (! $blog) {
            return $this->errorResponse('Blog not found', 404);
        }

        $seo = Seo::find($blog->seo_id);

        if ($request->hasFile('meta_image')) {
            $metaFile = FileUpload::storeFile($request->file('meta_image'), 'uploads/seos');
            $seo->meta_image = $metaFile;
        }

        $seo->meta_title = $request->meta_title;
        $seo->meta_description = $request->meta_description;
        $seo->meta_keywords = $request->meta_keywords;
        $seo->save();

        if ($request->hasFile('image')) {
            $file = FileUpload::storeFile($request->file('image'), 'uploads/blogs');
            $blog->image = $file;
        }

        $category = BlogCategory::find($request->category_id);

        $blog->title = $request->title;
        $blog->category_id = $category->id;
        $blog->category = $category->name;
        $blog->slug = $request->slug;
        $blog->description = $request->description;
        $blog->short_description = $request->short_description;
        $blog->save();

        return $this->successResponse($blog, 'Blog updated successfully');
    }

    public function blogDelete($id)
    {
        $blog = Blog::find($id);

        if (! $blog) {
            return $this->errorResponse('Blog not found', 404);
        }

        $blog->delete();

        return $this->successResponse(null, 'Blog deleted successfully');
    }

    public function blogDetails($id)
    {
        $blog = Blog::find($id);

        if (! $blog) {
            return $this->errorResponse('Blog not found', 404);
        }
        $reaturedPosts = Blog::where('id', '!=', $id)->latest()->take(5)->get();
        $category = BlogCategory::select('id', 'name')->get();

        return $this->successResponse([
            'blog' => $blog,
            'category' => $category,
            'reaturedPosts' => $reaturedPosts,
        ], 'Blog fetched successfully');
    }
}
