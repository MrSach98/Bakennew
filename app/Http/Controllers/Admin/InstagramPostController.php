<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InstagramPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class InstagramPostController extends Controller
{
    private string $uploadFolder = 'userassets/instagram';

    public function index()
    {
        return view('admin.instagram-posts.index');
    }

    public function data()
    {
        $posts = InstagramPost::select('instagram_posts.*')->orderBy('sort_order');

        return DataTables::of($posts)
            ->addColumn('thumbnail', function (InstagramPost $post) {
                return '<img src="' . asset($post->thumbnail_image) . '" width="60" height="60" class="rounded" style="object-fit:cover;">';
            })
            ->addColumn('link', function (InstagramPost $post) {
                return '<a href="' . e($post->post_url) . '" target="_blank" rel="noopener">' . Str::limit($post->post_url, 40) . '</a>';
            })
            ->addColumn('status', function (InstagramPost $post) {
                $checked = $post->is_active ? 'checked' : '';
                return '<div class="form-check form-switch">
                            <input type="checkbox" class="form-check-input status-toggle" role="switch"
                                   data-id="' . $post->id . '" ' . $checked . '>
                        </div>';
            })
            ->addColumn('actions', function (InstagramPost $post) {
                return '
                    <button class="btn btn-sm btn-outline-primary" onclick="openEditModal(' . $post->id . ')">Edit</button>
                    <button class="btn btn-sm btn-outline-danger" onclick="confirmDelete(' . $post->id . ')">Delete</button>
                    <form id="deleteForm' . $post->id . '" action="' . route('admin.instagram-posts.destroy', $post) . '" method="POST" class="d-none">
                        ' . csrf_field() . method_field('DELETE') . '
                    </form>
                ';
            })
            ->rawColumns(['thumbnail', 'link', 'status', 'actions'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validated = $this->validateData($request);
        $validated['thumbnail_image'] = $this->uploadImage($request->file('thumbnail_image'));

        InstagramPost::create($validated);

        return redirect()->route('admin.instagram-posts.index')->with('success', 'Instagram post added successfully.');
    }

    public function fetch(InstagramPost $instagramPost)
    {
        return response()->json([
            'id' => $instagramPost->id,
            'post_url' => $instagramPost->post_url,
            'caption' => $instagramPost->caption,
            'sort_order' => $instagramPost->sort_order,
            'is_active' => $instagramPost->is_active,
            'thumbnail_url' => asset($instagramPost->thumbnail_image),
        ]);
    }

    public function update(Request $request, InstagramPost $instagramPost)
    {
        $validated = $this->validateData($request, isEdit: true);

        if ($request->hasFile('thumbnail_image')) {
            $this->deleteImage($instagramPost->thumbnail_image);
            $validated['thumbnail_image'] = $this->uploadImage($request->file('thumbnail_image'));
        }

        $instagramPost->update($validated);

        return redirect()->route('admin.instagram-posts.index')->with('success', 'Instagram post updated successfully.');
    }

    public function destroy(InstagramPost $instagramPost)
    {
        $this->deleteImage($instagramPost->thumbnail_image);
        $instagramPost->delete();

        return back()->with('success', 'Instagram post deleted successfully.');
    }

    public function toggleStatus(InstagramPost $instagramPost)
    {
        $instagramPost->update(['is_active' => ! $instagramPost->is_active]);

        return response()->json(['is_active' => $instagramPost->is_active]);
    }

    private function validateData(Request $request, bool $isEdit = false): array
    {
        $data = $request->validate([
            'post_url' => ['required', 'url', 'max:500'],
            'thumbnail_image' => [$isEdit ? 'nullable' : 'required', 'image', 'max:2048'],
            'caption' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        if ($isEdit) {
            unset($data['thumbnail_image']);
        }

        return $data;
    }

    private function uploadImage($file): string
    {
        $destination = public_path($this->uploadFolder);

        if (! File::exists($destination)) {
            File::makeDirectory($destination, 0755, true);
        }

        $filename = time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
        $file->move($destination, $filename);

        return $this->uploadFolder . '/' . $filename;
    }

    private function deleteImage(?string $relativePath): void
    {
        if ($relativePath && File::exists(public_path($relativePath))) {
            File::delete(public_path($relativePath));
        }
    }
}