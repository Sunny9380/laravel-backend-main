<?php

namespace App\Http\Controllers\v1\admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\HttpResponse;
use App\Models\Configuration;
use App\Models\Page;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;

class PageController extends Controller
{
    use HttpResponse;

    public function getLogo()
    {
        try {
            $logo = Configuration::select('logo')->first();
            $logo = asset('storage/logo/' . $logo->logo);
            return $this->success(
                data: $logo
            );
        } catch (\Exception $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }

    public function addLogo(Request $request)
    {
        try {
            $validated = $request->validate([
                'logo' => 'required|image|mimes:jpeg,png,jpg|max:500',
            ]);

            $config = Configuration::first();
            $imageService = new ImageUploadService();
            if ($config->logo) {
                $imageService->deleteImage($config->logo, '/logo/');
            }
            $config->logo = $imageService->uploadImage($request->file('logo'), '/logo');
            if ($config->save()) {
                return $this->success(message: 'Logo added successfully');
            }

            return $this->error(message: 'Failed to add logo');

        } catch (\Exception $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }

    public function getAllPages()
    {
        try {
            $pages = Page::all();
            return $this->success(data: $pages);
        } catch (\Exception $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }

    public function togglePageStatus(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer',
            ]);

            $page = Page::find($validated['id']);
            $page->is_active = !$page->is_active;

            if ($page->save()) {
                return $this->success(message: 'Page status updated successfully');
            }
            return $this->error(message: 'Failed to update page status');

        } catch (\Exception $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }

    public function getPageForAdmin($slug)
    {
        try {
            $page = Page::where('slug', $slug)
                ->first();
            return $this->success(data: $page);
        } catch (\Exception $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }

    public function getPage($slug)
    {
        try {
            $page = Page::where('slug', $slug)
                ->where('is_active', 1)
                ->first();
            return $this->success(data: $page);
        } catch (\Exception $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }

    public function getAllAvailablePages()
    {
        try {
            $pages = Page::where('is_active', 1)->get();
            return $this->success(data: $pages);
        } catch (\Exception $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }

    public function addPage(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string',
                'content' => 'required|string',
                'meta_title' => 'nullable|string',
                'meta_description' => 'nullable|string',
            ]);

            $page = new Page();
            $page->title = $validated['title'];
            $page->content = $validated['content'];
            $page->meta_title = $validated['meta_title'];
            $page->meta_description = $validated['meta_description'];

            if ($page->save()) {
                return $this->success(message: 'Page added successfully');
            }
            return $this->error(message: 'Failed to add page');

        } catch (\Exception $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }

    public function updatePage(Request $request)
    {
        try {
            $validated = $request->validate([
                'slug' => 'required|exists:pages,slug',
                'title' => 'required|string',
                'content' => 'required|string',
                'meta_title' => 'nullable|string',
                'meta_description' => 'nullable|string',
            ]);

            $page = Page::where('slug', $validated['slug'])->first();
            $page->title = $validated['title'];
            $page->content = $validated['content'];
            $page->meta_title = $validated['meta_title'];
            $page->meta_description = $validated['meta_description'];

            if ($page->save()) {
                return $this->success(message: 'Page updated successfully');
            }
            return $this->error(message: 'Failed to update page');

        } catch (\Exception $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }

    public function deletePage(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|exists:pages,id',
            ]);

            $page = Page::where('id', $validated['id'])->first();
            if ($page->delete()) {
                return $this->success(message: 'Page deleted successfully');
            }
            return $this->error(message: 'Failed to delete page');

        } catch (\Exception $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }

}
