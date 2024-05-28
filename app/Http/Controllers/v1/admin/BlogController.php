<?php

namespace App\Http\Controllers\v1\admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\HttpResponse;
use App\Models\Blogs;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    use HttpResponse;

    public function getMostPopularBlog()
    {
        try {
            $blog = Blogs::where('is_active', 1)->orderBy('views', 'desc')->first();
            if (!$blog) {
                return $this->notFound(
                    message: 'No Blog Found!'
                );
            }
            $blog->image = asset('storage/blogs/' . $blog->image);
            return $this->success(
                data: $blog,
                message: 'Most Popular Blog!'
            );
        } catch (\Exception $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function getLatestBlogs()
    {
        try {
            $search = request()->query('search');
            $blogs = Blogs::where('is_active', 1)
                ->where('title', 'LIKE', "%{$search}%")
                ->orderBy('created_at', 'desc')
                ->paginate(15);

            if (!$blogs) {
                return $this->notFound(
                    message: 'No Blog Found!'
                );
            }
            foreach ($blogs as $blog) {
                $blog->image = asset('storage/blogs/' . $blog->image);
            }

            return $this->success(
                data: $blogs,
                message: 'All Blogs!'
            );
        } catch (\Exception $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function getBlogBySlug($slug)
    {
        try {
            $blog = Blogs::where('slug', $slug)
                ->where('is_active', 1)
                ->first();

            if (!$blog) {
                return $this->notFound(
                    message: 'Blog not found!'
                );
            }

            $blog->views = $blog->views + 1;
            $blog->update();

            $blog->image = asset('storage/blogs/' . $blog->image);
            return $this->success(
                data: $blog
            );
        } catch (\Exception $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function getPopularBlogs()
    {
        try {
            $search = request()->query('search');

            $blogs = Blogs::where('is_active', 1)
                ->where('title', 'LIKE', "%{$search}%")
                ->orderBy('views', 'desc')
                ->paginate(15);

            if (!$blogs) {
                return $this->notFound(
                    message: 'No Blog Found!'
                );
            }
            foreach ($blogs as $blog) {
                $blog->image = asset('storage/blogs/' . $blog->image);
            }
            return $this->success(
                data: $blogs,
                message: 'Most Popular Blogs!'
            );
        } catch (\Exception $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function allBlogs()
    {
        try {
            $search = request()->query('search');
            $blogs = Blogs::where('title', 'LIKE', "%{$search}%")->paginate(15);
            // getting blog image
            foreach ($blogs as $blog) {
                $blog->image = asset('storage/blogs/' . $blog->image);
            }
            return $this->success(
                data: $blogs
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function getBlog($id)
    {
        try {
            $blog = Blogs::find($id);
            if (!$blog) {
                return $this->notFound(
                    message: 'Blog not found!'
                );
            }
            return $this->success(
                data: $blog
            );
        } catch (\Exception $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function updateBlog(Request $request)
    {

        try {
            $validated = $request->validate([
                'id' => 'required|integer',
                'title' => 'required|string',
                'image' => 'nullable|max:2048',
                'meta_title' => 'required|',
                'meta_description' => 'required',
                'meta_keywords' => 'required|string',
                'body' => 'nullable|string'
            ]);
            $blog = Blogs::find($validated['id']);
            if (!$blog) {
                return $this->notFound(
                    message: 'Blog not found!'
                );
            }
            $blog->title = $validated['title'];
            $blog->meta_title = $validated['meta_title'];
            $blog->meta_description = $validated['meta_description'];
            $blog->meta_keywords = $validated['meta_keywords'];
            $blog->body = $validated['body'];
            if ($request->hasFile('image')) {
                // deleting old image
                if ($blog->image) {
                    $imageService = new ImageUploadService();
                    $imageService->deleteImage($blog->image, '/blogs/');
                }
                $imageService = new ImageUploadService();
                $blog->image = $imageService->uploadImage($request->file('image'), '/blogs/');
            }
            if ($blog->update()) {
                return $this->success(
                    message: 'Blog Updated Successfully!'
                );
            }
            return $this->error(
                message: 'Failed to update Blog!'
            );
        } catch (\Exception $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function addBlog(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'meta_title' => 'required|string',
            'meta_description' => 'required',
            'meta_keywords' => 'required|string',
            'body' => 'nullable|string'
        ]);

        try {
            $blog = new Blogs();
            $blog->title = $validated['title'];
            $blog->meta_title = $validated['meta_title'];
            $blog->meta_description = $validated['meta_description'];
            $blog->meta_keywords = $validated['meta_keywords'];
            $blog->body = $validated['body'];
            if ($request->hasFile('image')) {
                $imageService = new ImageUploadService();
                $blog->image = $imageService->uploadImage($request->file('image'), '/blogs/');
            }
            if ($blog->save()) {
                return $this->success(
                    message: 'Blog Added Successfully!'
                );
            }
            return $this->error(
                message: 'Failed to add Blog!'
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function deleteBlog(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:blogs,id',
        ]);

        try {
            $blog = Blogs::find($validated['id']);
            if ($blog->delete()) {
                return $this->success(
                    message: 'Blog Deleted Successfully!'
                );
            }
            return $this->error(
                message: 'Failed to delete Blog!'
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function toggleBlogStatus(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:blogs,id',
        ]);

        try {
            $blog = Blogs::find($validated['id']);
            $blog->is_active = !$blog->is_active;
            if ($blog->update()) {
                return $this->success(
                    message: 'Blog Status Updated Successfully!'
                );
            }
            return $this->error(
                message: 'Failed to update Blog Status!'
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }
}
