<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Helpers\ResponseHelper;

class ProjectController extends Controller
{
    // ── Public ────────────────────────────────────────────────────────────────

    public function index(): JsonResponse
    {
        $projects = Project::where('published', true)->latest()->get()->map(fn($p) => $this->format($p));
        return ResponseHelper::success($projects, 'Projects retrieved');
    }

    // ── Admin ─────────────────────────────────────────────────────────────────

    public function adminIndex(): JsonResponse
    {
        $projects = Project::withTrashed()->latest()->get()->map(fn($p) => $this->format($p));
        return ResponseHelper::success($projects, 'Projects retrieved');
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title'            => 'required|string|max:255',
            'category'         => 'nullable|string|max:100',
            'description'      => 'nullable|string',
            'full_description' => 'nullable|string',
            'image'            => 'nullable|image|max:5120',
            'published'        => 'nullable|boolean',
        ]);

        $data = $request->only(['title', 'category', 'description', 'full_description', 'published']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('projects', 'public');
        }

        $project = Project::create($data);
        return ResponseHelper::created($this->format($project), 'Project created');
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $project = Project::findOrFail($id);

        $request->validate([
            'title'            => 'nullable|string|max:255',
            'category'         => 'nullable|string|max:100',
            'description'      => 'nullable|string',
            'full_description' => 'nullable|string',
            'image'            => 'nullable|image|max:5120',
            'published'        => 'nullable|boolean',
        ]);

        $data = $request->only(['title', 'category', 'description', 'full_description', 'published']);

        if ($request->hasFile('image')) {
            if ($project->image) Storage::disk('public')->delete($project->image);
            $data['image'] = $request->file('image')->store('projects', 'public');
        }

        $project->update($data);
        return ResponseHelper::success($this->format($project->fresh()), 'Project updated');
    }

    public function destroy(int $id): JsonResponse
    {
        $project = Project::findOrFail($id);
        if ($project->image) Storage::disk('public')->delete($project->image);
        $project->delete();
        return ResponseHelper::success(null, 'Project deleted');
    }

    // ── Helper ────────────────────────────────────────────────────────────────

    private function format(Project $project): array
    {
        $base = config('app.url') . '/storage/';
        return [
            'id'               => $project->id,
            'title'            => $project->title,
            'category'         => $project->category,
            'description'      => $project->description,
            'full_description' => $project->full_description,
            'published'        => $project->published,
            'image'            => $project->image ? $base . $project->image : null,
            'created_at'       => $project->created_at?->toIso8601String(),
            'deleted_at'       => $project->deleted_at?->toIso8601String(),
        ];
    }
}
