<?php

namespace App\Http\Controllers;

use App\Models\Ai\Assignment;
use App\Models\Ai\AssignmentSection;
use App\Models\Ai\UserAssignment;
use App\Models\Ai\UserAssignmentContent;
use App\Services\Ai\AssignmentAiService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserAssignmentController extends Controller
{
    public function __construct(
        private readonly AssignmentAiService $aiService
    ) {}

    // ── Assignment list ────────────────────────────────────────

    public function index(Request $request)
    {
        $userId = $request->user()->id;
    
        $assignments = Assignment::published()
            ->withCount('sections')
            ->with(['userAssignments' => function ($q) use ($userId) {
                $q->where('user_id', $userId)
                  // IMPORTANT: Add 'identifier' here!
                  ->select(['id', 'assignment_id', 'user_id', 'status', 'sections_filled', 'total_sections', 'updated_at', 'identifier']);
            }])
            ->orderByDesc('created_at')
            // IMPORTANT: Ensure 'identifier' is fetched for the master assignment too!
            ->get(['id', 'title', 'subject', 'course_code', 'description', 'is_public', 'created_at', 'identifier']);
    
        return view('assignments.index', compact('assignments', 'userId'));
    }


    // ── Open workspace ──────────────────────────────────────────

    public function workspace(Request $request, Assignment $assignment)
{
    // 1. Laravel uses 'assignments.identifier' to find $assignment
    if (!$assignment->is_published) {
        abort(404);
    }

    $userId = $request->user()->id;

    // 2. Find or Create the user's progress based on the Master ID
    // We don't use the UserAssignment slug for the URL lookup here.
    $userAssignment = UserAssignment::firstOrCreate(
        [
            'user_id' => $userId, 
            'assignment_id' => $assignment->id
        ],
        [
            'status'          => 'draft',
            'total_sections'  => $assignment->sections()->count(),
            'sections_filled' => 0,
            // A unique slug is still generated for internal use/exports
            'identifier'      => \Illuminate\Support\Str::random(30), 
        ]
    );

    // 3. Load sections & content
    $sections = $assignment->sections;
    $contents = UserAssignmentContent::where('user_assignment_id', $userAssignment->id)
        ->get()
        ->keyBy('section_id');

    return view('assignments.workspace', compact(
        'assignment', 
        'userAssignment', 
        'sections', 
        'contents'
    ));
}



    // ── Save section content (AJAX) ────────────────────────────
    //
    // Called by debounced auto-save AND manual save button.
    // Uses updateOrInsert → 1 upsert query per section.
    // Does NOT save N sections in a loop — saves ONE at a time.

    public function saveContent(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_assignment_id' => ['required', 'integer'],
            'section_id'         => ['required', 'integer'],
            'content'            => ['required', 'string', 'max:50000'],
        ]);

        $userId = $request->user()->id;

        // Verify ownership (prevents IDOR)
        $userAssignment = UserAssignment::where('id', $validated['user_assignment_id'])
            ->where('user_id', $userId)
            ->firstOrFail();

        $wordCount = str_word_count(strip_tags($validated['content']));

        // Upsert: insert if not exists, update if exists — 1 query
        DB::connection('ai')->table('user_assignment_contents')->upsert(
            [
                [
                    'user_assignment_id' => $userAssignment->id,
                    'section_id'         => $validated['section_id'],
                    'content'            => $validated['content'],
                    'word_count'         => $wordCount,
                    'updated_at'         => now(),
                ]
            ],
            uniqueBy: ['user_assignment_id', 'section_id'],
            update:   ['content', 'word_count', 'updated_at']
        );

        // Update sections_filled count (1 count query)
        $filled = DB::connection('ai')
            ->table('user_assignment_contents')
            ->where('user_assignment_id', $userAssignment->id)
            ->where('word_count', '>', 10)
            ->count();

        $userAssignment->update(['sections_filled' => $filled]);

        return response()->json([
            'success'    => true,
            'word_count' => $wordCount,
            'progress'   => $userAssignment->getProgressPercent(),
        ]);
    }

    // ── Batch save all sections (manual Save All) ──────────────

    public function saveAll(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_assignment_id' => ['required', 'integer'],
            'sections'           => ['required', 'array', 'max:20'],
            'sections.*.section_id' => ['required', 'integer'],
            'sections.*.content'    => ['required', 'string', 'max:50000'],
        ]);

        $userId = $request->user()->id;

        $userAssignment = UserAssignment::where('id', $validated['user_assignment_id'])
            ->where('user_id', $userId)
            ->firstOrFail();

        $rows = [];
        $now  = now();

        foreach ($validated['sections'] as $s) {
            if (empty(trim($s['content']))) {
                continue;
            }

            $rows[] = [
                'user_assignment_id' => $userAssignment->id,
                'section_id'         => $s['section_id'],
                'content'            => $s['content'],
                'word_count'         => str_word_count(strip_tags($s['content'])),
                'updated_at'         => $now,
            ];
        }

        if (!empty($rows)) {
            // ONE bulk upsert — regardless of how many sections
            DB::connection('ai')->table('user_assignment_contents')->upsert(
                $rows,
                uniqueBy: ['user_assignment_id', 'section_id'],
                update:   ['content', 'word_count', 'updated_at']
            );
        }

        $filled = DB::connection('ai')
            ->table('user_assignment_contents')
            ->where('user_assignment_id', $userAssignment->id)
            ->where('word_count', '>', 10)
            ->count();

        $userAssignment->update([
            'sections_filled' => $filled,
            'status'          => $filled >= $userAssignment->total_sections ? 'completed' : 'draft',
        ]);

        return response()->json([
            'success'  => true,
            'progress' => $userAssignment->getProgressPercent(),
            'status'   => $userAssignment->status,
        ]);
    }

    // ── AI: Generate section content ───────────────────────────

    public function generateSection(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'section_id'         => ['required', 'integer'],
            'user_assignment_id' => ['required', 'integer'],
            'knowledge_base_id'  => ['nullable', 'integer'],
        ]);

        $userId = $request->user()->id;

        // Verify ownership
        $userAssignment = UserAssignment::where('id', $validated['user_assignment_id'])
            ->where('user_id', $userId)
            ->with('assignment:id,topic,course')
            ->firstOrFail();

        $section = AssignmentSection::findOrFail($validated['section_id']);

        $result = $this->aiService->generateSection(
            section: $section,
            topic:   $userAssignment->assignment->topic,
            course:  $userAssignment->assignment->course ?? '',
            userId:  $userId,
            baseId:  $validated['knowledge_base_id'] ?? null
        );

        return response()->json([
            'success'   => true,
            'text'      => $result['text'],
            'provider'  => $result['provider'],
            'used_kb'   => $result['used_kb'],
            'from_cache'=> $result['from_cache'] ?? false,
        ]);
    }

    // ── AI: Improve existing content ───────────────────────────

    public function improveContent(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'section_id'         => ['required', 'integer'],
            'user_assignment_id' => ['required', 'integer'],
            'content'            => ['required', 'string', 'min:20', 'max:10000'],
        ]);

        $userId = $request->user()->id;

        $userAssignment = UserAssignment::where('id', $validated['user_assignment_id'])
            ->where('user_id', $userId)
            ->with('assignment:id,topic')
            ->firstOrFail();

        $section = AssignmentSection::findOrFail($validated['section_id']);

        $result = $this->aiService->improveContent(
            section:         $section,
            existingContent: $validated['content'],
            topic:           $userAssignment->assignment->topic
        );

        if (!$result['success']) {
            return response()->json(['success' => false, 'message' => 'AI improvement failed. Please try again.'], 422);
        }

        return response()->json([
            'success'  => true,
            'text'     => $result['text'],
            'provider' => $result['provider'],
        ]);
    }

    // ── Mark completed ──────────────────────────────────────────

    public function markCompleted(Request $request, UserAssignment $userAssignment): JsonResponse
    {
        if ($userAssignment->user_id !== $request->user()->id) {
            abort(403);
        }

        $userAssignment->update(['status' => 'completed']);

        return response()->json(['success' => true]);
    }

    // ── PDF Export ─────────────────────────────────────────────

    public function exportPdf(Request $request, UserAssignment $userAssignment)
    {
        if ((int) $userAssignment->user_id !== (int) $request->user()->id) {
            abort(403);
        }


        // Load everything needed — 3 queries:
        // 1. Assignment + sections
        // 2. User info
        // 3. Contents
        $assignment = Assignment::with('sections')->find($userAssignment->assignment_id);

        $contents = UserAssignmentContent::where('user_assignment_id', $userAssignment->id)
            ->get()
            ->keyBy('section_id');

        $user = $request->user();

        $pdf = Pdf::loadView('assignments.pdf', compact('assignment', 'userAssignment', 'contents', 'user'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => false,
                'defaultFont'          => 'serif',
                'dpi'                  => 96,
            ]);

        $filename = str_replace(' ', '_', $assignment->title) . '_' . $user->id . '.pdf';

        return $pdf->download($filename);
    }
}
