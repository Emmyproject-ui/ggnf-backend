<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Helpers\ResponseHelper;

class BlogPostController extends Controller
{
    // ── Public ────────────────────────────────────────────────────────────────

    /** GET /api/blog  — list published posts */
    public function index(): JsonResponse
    {
        $posts = BlogPost::where('published', true)
            ->latest()
            ->get()
            ->map(fn($p) => $this->format($p));

        return ResponseHelper::success($posts, 'Blog posts retrieved');
    }

    /** GET /api/blog/{slug}  — single post */
    public function show(string $slug): JsonResponse
    {
        $post = BlogPost::where('slug', $slug)->where('published', true)->firstOrFail();
        return ResponseHelper::success($this->format($post), 'Post retrieved');
    }

    // ── Admin ─────────────────────────────────────────────────────────────────

    /** GET /api/admin/blog  — all posts (including unpublished) */
    public function adminIndex(): JsonResponse
    {
        $posts = BlogPost::withTrashed()->latest()->get()->map(fn($p) => $this->format($p));
        return ResponseHelper::success($posts, 'Blog posts retrieved');
    }

    /** POST /api/admin/blog  — create */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title'    => 'required|string|max:255',
            'excerpt'  => 'nullable|string',
            'content'  => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'image'    => 'nullable|image|max:5120',
            'gallery.*' => 'nullable|image|max:5120',
            'published' => 'nullable|boolean',
        ]);

        $data = $request->only(['title', 'excerpt', 'content', 'category', 'published']);
        $data['slug'] = Str::slug($request->title);

        // Ensure unique slug (including soft-deleted records)
        $base = $data['slug'];
        $i = 1;
        while (BlogPost::withTrashed()->where('slug', $data['slug'])->exists()) {
            $data['slug'] = $base . '-' . $i++;
        }

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('blog', 'public');
        }

        if ($request->hasFile('gallery')) {
            $data['gallery'] = collect($request->file('gallery'))
                ->map(fn($f) => $f->store('blog', 'public'))
                ->values()
                ->toArray();
        }

        $post = BlogPost::create($data);
        return ResponseHelper::created($this->format($post), 'Post created');
    }

    /** POST /api/admin/blog/{id}  — update (form-data friendly) */
    public function update(Request $request, int $id): JsonResponse
    {
        $post = BlogPost::findOrFail($id);

        $request->validate([
            'title'    => 'nullable|string|max:255',
            'excerpt'  => 'nullable|string',
            'content'  => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'image'    => 'nullable|image|max:5120',
            'gallery.*' => 'nullable|image|max:5120',
            'published' => 'nullable|boolean',
        ]);

        $data = $request->only(['title', 'excerpt', 'content', 'category', 'published']);

        if ($request->hasFile('image')) {
            // Delete old image
            if ($post->image) Storage::disk('public')->delete($post->image);
            $data['image'] = $request->file('image')->store('blog', 'public');
        }

        if ($request->hasFile('gallery')) {
            foreach ((array) $post->gallery as $old) {
                Storage::disk('public')->delete($old);
            }
            $data['gallery'] = collect($request->file('gallery'))
                ->map(fn($f) => $f->store('blog', 'public'))
                ->values()
                ->toArray();
        }

        $post->update($data);
        return ResponseHelper::success($this->format($post->fresh()), 'Post updated');
    }

    /** DELETE /api/admin/blog/{id} */
    public function destroy(int $id): JsonResponse
    {
        $post = BlogPost::findOrFail($id);
        if ($post->image) Storage::disk('public')->delete($post->image);
        foreach ((array) $post->gallery as $path) {
            Storage::disk('public')->delete($path);
        }
        $post->delete();
        return ResponseHelper::success(null, 'Post deleted');
    }

    // ── Shared image upload ───────────────────────────────────────────────────

    /** POST /api/admin/upload — standalone image upload, returns URL */
    public function upload(Request $request): JsonResponse
    {
        $request->validate(['image' => 'required|image|max:5120']);
        $path = $request->file('image')->store('uploads', 'public');
        return ResponseHelper::success([
            'path' => $path,
            'url'  => Storage::disk('public')->url($path),
        ], 'Image uploaded');
    }

    // ── Helper ────────────────────────────────────────────────────────────────

    private function format(BlogPost $post): array
    {
        $base = config('app.url') . '/storage/';
        return [
            'id'         => $post->id,
            'title'      => $post->title,
            'slug'       => $post->slug,
            'excerpt'    => $post->excerpt,
            'content'    => $post->content,
            'category'   => $post->category,
            'date'       => $post->created_at?->format('M j, Y'),
            'published'  => $post->published,
            'image'      => $post->image ? $base . $post->image : null,
            'gallery'    => collect($post->gallery ?? [])->map(fn($p) => $base . $p)->values(),
            'created_at' => $post->created_at?->toIso8601String(),
            'deleted_at' => $post->deleted_at?->toIso8601String(),
        ];
    }
}
