<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MaterialController extends Controller
{
    public function index()
    {
        $materials = Material::with('resource')->latest()->paginate(5);
        return view('admin.materials.index', compact('materials'));
    }

    public function create()
    {
        $resources = Resource::all();
        return view('admin.materials.create', compact('resources'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'resource_id' => 'required|exists:resources,id',
            'type' => 'required|in:note,pdf,doc,video,other',
            'file' => 'nullable|file|max:20480',
            'note_text' => 'nullable|string',
        ]);

        // Unique slug
        $slug = Str::slug($validated['title']);
        $originalSlug = $slug;
        $count = 1;

        while (Material::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        $relativePath = null;
        $publicUrl = null;

        /** FILE UPLOAD **/
        if ($request->hasFile('file')) {

            $file = $request->file('file');

            // Create a clean filename
            $filename = Str::slug($request->title) . '.' . $file->getClientOriginalExtension();

            // Ensure it's unique
            $counter = 1;
            while (file_exists(storage_path("app/public/materials/$filename"))) {
                $filename = Str::slug($request->title) . "-$counter." . $file->getClientOriginalExtension();
                $counter++;
            }

            // Store file inside storage/app/public/materials
            $relativePath = $file->storeAs('materials', $filename, 'public');

            // Public URL (correct for public/storage disk)
            $publicUrl = asset("storage/" . $relativePath);
        }

        // Save set
        Material::create([
            'title' => $validated['title'],
            'resource_id' => $validated['resource_id'],
            'slug' => $slug,
            'type' => $validated['type'],
            'pdf_path' => $publicUrl,     // Full URL
            'file_path' => $relativePath, // Relative path
            'note_text' => $validated['note_text'] ?? null,
        ]);

        return redirect()->route('admin.materials.index')
            ->with('success', 'Material uploaded successfully.');
    }



    public function edit(Material $material)
    {
        $resources = Resource::all();
        return view('admin.materials.edit', compact('material', 'resources'));
    }


    public function update(Request $request, Material $material)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'resource_id' => 'required|exists:resources,id',
            'type' => 'required|in:note,pdf,doc,video,other',
            'file' => 'nullable|file|max:20480',
            'note_text' => 'nullable|string',
        ]);

        // Regenerate slug if needed
        if ($material->title !== $validated['title']) {

            $slug = Str::slug($validated['title']);
            $originalSlug = $slug;
            $count = 1;

            while (Material::where('slug', $slug)->where('id', '!=', $material->id)->exists()) {
                $slug = "{$originalSlug}-{$count}";
                $count++;
            }

            $material->slug = $slug;
        }

        $relativePath = $material->file_path;
        $publicUrl = $material->pdf_path;

        /** NEW FILE **/
        if ($request->hasFile('file')) {

            // Delete old file
            if ($material->file_path) {
                $oldPath = storage_path("app/public/" . $material->file_path);
                if (file_exists($oldPath)) unlink($oldPath);
            }

            $file = $request->file('file');
            $filename = Str::slug($request->title) . '.' . $file->getClientOriginalExtension();

            $counter = 1;
            while (file_exists(storage_path("app/public/materials/$filename"))) {
                $filename = Str::slug($request->title) . "-$counter." . $file->getClientOriginalExtension();
                $counter++;
            }

            // Store it
            $relativePath = $file->storeAs('materials', $filename, 'public');

            // Public URL
            $publicUrl = asset("storage/" . $relativePath);
        }

        // Update model
        $material->update([
            'title' => $validated['title'],
            'resource_id' => $validated['resource_id'],
            'type' => $validated['type'],
            'slug' => $material->slug,
            'pdf_path' => $publicUrl,
            'file_path' => $relativePath,
            'note_text' => $validated['note_text'] ?? null,
        ]);

        return redirect()->route('admin.materials.index')
            ->with('success', 'Material updated successfully.');
    }


    public function destroy(Material $material)
    {
        // Delete file
        if ($material->file_path) {
            $path = storage_path("app/public/" . $material->file_path);
            if (file_exists($path)) unlink($path);
        }

        $material->delete();

        return redirect()->route('admin.materials.index')
            ->with('success', 'Material deleted.');
    }


    public function show($id)
    {
        $material = Material::findOrFail($id);
        return view('materials.show', compact('material'));
    }
}
