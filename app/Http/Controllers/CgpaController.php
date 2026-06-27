<?php

namespace App\Http\Controllers;

use App\Models\CgpaCourse;
use App\Models\CgpaCourseOption;
use App\Models\Semester;
use App\Services\CgpaCalculatorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CgpaController extends Controller
{
    public function __construct(
        private readonly CgpaCalculatorService $calculator
    ) {}

    // ── Dashboard ─────────────────────────────────────────────────

    public function dashboard(): View
    {
        $user      = Auth::user();
        $stats     = $this->calculator->dashboardStats($user);
        $breakdown = $this->calculator->semesterBreakdown($user);

        return view('cgpa.dashboard', compact('stats', 'breakdown'));
    }

    // ── Semesters Index ───────────────────────────────────────────

    public function semesterIndex(): View
    {
        $user      = Auth::user();
        $breakdown = $this->calculator->semesterBreakdown($user);
        $cgpa      = $this->calculator->overallCgpa($user);

        return view('cgpa.semesters.index', compact('breakdown', 'cgpa'));
    }

    // ── Semester Store ────────────────────────────────────────────

    public function semesterStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'level'         => ['required', 'in:100,200,300,400'],
            'semester_type' => ['required', 'in:1,2'],
        ]);

        $exists = Semester::where('user_id', Auth::id())
            ->where('level', $validated['level'])
            ->where('semester_type', $validated['semester_type'])
            ->exists();

        if ($exists) {
            return back()->withErrors([
                'duplicate' => 'That semester already exists in your record.',
            ]);
        }

        $semester = Semester::create([
            'user_id'       => Auth::id(),
            'level'         => $validated['level'],
            'semester_type' => $validated['semester_type'],
        ]);

        return redirect()
            ->route('cgpa.semester.show', $semester)
            ->with('success', 'Semester created. Start adding your courses!');
    }

    // ── Semester Show ─────────────────────────────────────────────

    public function semesterShow(Semester $semester): View
    {
        $this->authoriseSemester($semester);

        $semester->load('courses');
        $gpa          = $this->calculator->semesterGpa($semester);
        $cgpa         = $this->calculator->overallCgpa(Auth::user());
        $gradeOptions = CgpaCourse::GRADE_MAP;

        // Predefined course options for this level + semester
        $courseOptions = CgpaCourseOption::optionsFor(
            $semester->level,
            $semester->semester_type
        );

        // JSON for JS auto-fill (unit pre-population on course select)
        $courseOptionsJson = CgpaCourseOption::jsonFor(
            $semester->level,
            $semester->semester_type
        );

        return view('cgpa.semesters.show', compact(
            'semester', 'gpa', 'cgpa', 'gradeOptions',
            'courseOptions', 'courseOptionsJson'
        ));
    }

    // ── Semester Destroy ──────────────────────────────────────────

    public function semesterDestroy(Semester $semester): RedirectResponse
    {
        $this->authoriseSemester($semester);
        $semester->delete();

        return redirect()
            ->route('cgpa.semester.index')
            ->with('success', 'Semester and all its courses have been deleted.');
    }

    // ── Course Store ──────────────────────────────────────────────

    public function courseStore(Request $request, Semester $semester): RedirectResponse
    {
        $this->authoriseSemester($semester);

        $validated = $request->validate([
            'course_code'  => ['required', 'string', 'max:20'],
            'unit'         => ['required', 'integer', 'min:1', 'max:6'],
            'grade_letter' => ['required', 'in:A,B,C,D,E,F'],
        ]);

        $validated['course_code'] = strtoupper(trim($validated['course_code']));

        $exists = $semester->courses()
            ->where('course_code', $validated['course_code'])
            ->exists();

        if ($exists) {
            return back()->withErrors([
                'course_code' => "Course {$validated['course_code']} already exists in this semester.",
            ])->withInput();
        }

        CgpaCourse::create([
            'semester_id'  => $semester->id,
            'course_code'  => $validated['course_code'],
            'unit'         => $validated['unit'],
            'grade_letter' => $validated['grade_letter'],
        ]);

        return back()->with('success', "{$validated['course_code']} added successfully.");
    }

    // ── Course Update ─────────────────────────────────────────────

    public function courseUpdate(Request $request, Semester $semester, CgpaCourse $course): RedirectResponse
    {
        $this->authoriseSemester($semester);
        $this->authoriseCourse($course, $semester);

        $validated = $request->validate([
            'course_code'  => ['required', 'string', 'max:20'],
            'unit'         => ['required', 'integer', 'min:1', 'max:6'],
            'grade_letter' => ['required', 'in:A,B,C,D,E,F'],
        ]);

        $validated['course_code'] = strtoupper(trim($validated['course_code']));

        $exists = $semester->courses()
            ->where('course_code', $validated['course_code'])
            ->where('id', '!=', $course->id)
            ->exists();

        if ($exists) {
            return back()->withErrors([
                'course_code' => "Course {$validated['course_code']} already exists in this semester.",
            ]);
        }

        $course->update([
            'course_code'  => $validated['course_code'],
            'unit'         => $validated['unit'],
            'grade_letter' => $validated['grade_letter'],
        ]);

        return back()->with('success', 'Course updated successfully.');
    }

    // ── Course Destroy ────────────────────────────────────────────

    public function courseDestroy(Semester $semester, CgpaCourse $course): RedirectResponse
    {
        $this->authoriseSemester($semester);
        $this->authoriseCourse($course, $semester);

        $code = $course->course_code;
        $course->delete();

        return back()->with('success', "{$code} removed from this semester.");
    }

    // ── Private Authorisation Helpers ─────────────────────────────

    private function authoriseSemester(Semester $semester): void
    {
        abort_unless(
            (int) $semester->user_id === (int) Auth::id(),
            403,
            'You are not authorised to access this semester.'
        );
    }


    private function authoriseCourse(CgpaCourse $course, Semester $semester): void
    {
        abort_unless(
            (int) $course->semester_id === (int) $semester->id && 
            (int) $semester->user_id === (int) Auth::id(),
            403,
            'Unauthorized access to this course.'
        );
    }
}