<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ResourceController extends Controller
{
    public function index()
    {
        $resources = Resource::latest()->paginate(10);
        return view('admin.resources.index', compact('resources'));
    }

    public function create()
    {
        return view('admin.resources.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_code' => 'required|max:20|unique:resources,course_code',
            'course_title' => 'required|max:255',
            'lecturer' => 'required|max:255',
        ]);

        Resource::create([
            'course_code' => $validated['course_code'],
            'course_title' => $validated['course_title'],
            'lecturer' => $validated['lecturer'],
            'slug' => Str::slug($validated['course_title']),
        ]);

        return redirect()->route('admin.resources.index')->with('success', 'Course added.');
    }

    public function edit(Resource $resource)
    {
        return view('admin.resources.edit', compact('resource'));
    }

    public function update(Request $request, Resource $resource)
    {
        $validated = $request->validate([
            'course_code' => 'required|max:20|unique:resources,course_code,' . $resource->id,
            'course_title' => 'required|max:255',
            'lecturer' => 'required|max:255',
        ]);

        $resource->update($validated);
        return redirect()->route('admin.resources.index')->with('success', 'Course updated.');
    }

    public function destroy(Resource $resource)
    {
        $resource->delete();
        return redirect()->route('admin.resources.index')->with('success', 'Course deleted.');
    }
}
