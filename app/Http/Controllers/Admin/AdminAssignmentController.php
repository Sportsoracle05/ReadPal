<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ai\Assignment;
use App\Models\Ai\AssignmentSection;
use App\Models\Ai\UserAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * AdminAssignmentController
 *
 * Only admin users can access these routes.
 * Protect with: middleware('admin') or middleware('can:admin')
 * (adjust to your existing admin gate/middleware)
 */
class AdminAssignmentController extends Controller
{
    // ── List all assignments ───────────────────────────────────

    public function index()
    {
        // withCount uses a subquery — single round trip
        $assignments = Assignment::withCount(['sections', 'userAssignments'])
            ->orderByDesc('created_at')
            ->get();

        return view('admin.assignments.index', compact('assignments'));
    }

    // ── Create form ────────────────────────────────────────────

    public function create()
    {
        return view('admin.assignments.create');
    }

    // ── Store new assignment + sections ───────────────────────

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'                          => ['required', 'string', 'max:200'],
            'topic'                          => ['required', 'string', 'max:200'],
            'course'                         => ['nullable', 'string', 'max:100'],
            'description'                    => ['nullable', 'string', 'max:2000'],
            'is_published'                   => ['boolean'],
            'sections'                       => ['required', 'array', 'min:1', 'max:20'],
            'sections.*.title'               => ['required', 'string', 'max:200'],
            'sections.*.questions'           => ['nullable', 'string'], // newline-separated
            'sections.*.guidance_note'       => ['nullable', 'string', 'max:500'],
        ]);

        DB::connection('ai')->transaction(function () use ($validated, $request) {
            $assignment = Assignment::create([
                'created_by'   => $request->user()->id,
                'title'        => $validated['title'],
                'topic'        => $validated['topic'],
                'course'       => $validated['course'] ?? null,
                'description'  => $validated['description'] ?? null,
                'is_published' => $validated['is_published'] ?? false,
            ]);

            foreach ($validated['sections'] as $index => $sectionData) {
                // Parse newline-separated questions into a clean array
                $questions = $this->parseQuestions($sectionData['questions'] ?? '');

                AssignmentSection::create([
                    'assignment_id' => $assignment->id,
                    'title'         => $sectionData['title'],
                    'questions'     => $questions,
                    'guidance_note' => $sectionData['guidance_note'] ?? null,
                    'position'      => $index,
                ]);
            }
        });

        return redirect()->route('admin.assignments.index')
                         ->with('success', 'Assignment created successfully.');
    }

    // ── Edit form ──────────────────────────────────────────────

    public function edit(Assignment $assignment)
    {
        $assignment->load('sections');
        return view('admin.assignments.edit', compact('assignment'));
    }

    // ── Update ─────────────────────────────────────────────────

    public function update(Request $request, Assignment $assignment)
    {
        $validated = $request->validate([
            'title'                          => ['required', 'string', 'max:200'],
            'topic'                          => ['required', 'string', 'max:200'],
            'course'                         => ['nullable', 'string', 'max:100'],
            'description'                    => ['nullable', 'string', 'max:2000'],
            'is_published'                   => ['boolean'],
            'sections'                       => ['required', 'array', 'min:1'],
            'sections.*.title'               => ['required', 'string', 'max:200'],
            'sections.*.questions'           => ['nullable', 'string'],
            'sections.*.guidance_note'       => ['nullable', 'string', 'max:500'],
        ]);

        DB::connection('ai')->transaction(function () use ($validated, $assignment) {
            $assignment->update([
                'title'        => $validated['title'],
                'topic'        => $validated['topic'],
                'course'       => $validated['course'] ?? null,
                'description'  => $validated['description'] ?? null,
                'is_published' => $validated['is_published'] ?? false,
            ]);

            // Simple approach: delete existing sections and recreate
            // (sections are rarely edited; this is cleaner than diffing)
            $assignment->sections()->delete();

            foreach ($validated['sections'] as $index => $sectionData) {
                AssignmentSection::create([
                    'assignment_id' => $assignment->id,
                    'title'         => $sectionData['title'],
                    'questions'     => $this->parseQuestions($sectionData['questions'] ?? ''),
                    'guidance_note' => $sectionData['guidance_note'] ?? null,
                    'position'      => $index,
                ]);
            }
        });

        // Clear cached structure since it changed
        Assignment::clearCache($assignment->id);

        return redirect()->route('admin.assignments.index')
                         ->with('success', 'Assignment updated.');
    }

    // ── Toggle publish ─────────────────────────────────────────

    public function togglePublish(Assignment $assignment)
    {
        $assignment->update(['is_published' => !$assignment->is_published]);
        Assignment::clearCache($assignment->id);

        return back()->with('success',
            $assignment->is_published ? 'Assignment published.' : 'Assignment unpublished.'
        );
    }

    // ── Delete ─────────────────────────────────────────────────

    public function destroy(Assignment $assignment)
    {
        $assignment->delete(); // soft delete
        Assignment::clearCache($assignment->id);

        return redirect()->route('admin.assignments.index')
                         ->with('success', 'Assignment deleted.');
    }

    // ── Assignment submissions overview ───────────────────────

    public function submissions(Assignment $assignment)
    {
        $submissions = UserAssignment::where('assignment_id', $assignment->id)
            ->with('contents')
            ->orderByDesc('updated_at')
            ->paginate(20);

        return view('admin.assignments.submissions', compact('assignment', 'submissions'));
    }

    // ── Helpers ────────────────────────────────────────────────

    private function parseQuestions(string $raw): array
    {
        if (empty(trim($raw))) {
            return [];
        }

        return array_values(array_filter(
            array_map('trim', explode("\n", $raw)),
            fn($q) => strlen($q) >= 5
        ));
    }
}
