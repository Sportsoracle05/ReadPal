<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CgpaCourseOption;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CgpaCourseOptionController extends Controller
{
    public function index(Request $request): View
    {
        $level         = $request->integer('level', 300);
        $semesterType  = $request->integer('semester_type', 1);

        $options = CgpaCourseOption::where('level', $level)
            ->where('semester_type', $semesterType)
            ->orderBy('course_code')
            ->get();

        $allLevels       = [100, 200, 300, 400];
        $allSemesterTypes = [1 => '1st Semester', 2 => '2nd Semester'];

        return view('admin.cgpa.index', compact(
            'options', 'level', 'semesterType', 'allLevels', 'allSemesterTypes'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'level'         => ['required', 'in:100,200,300,400'],
            'semester_type' => ['required', 'in:1,2'],
            'course_code'   => ['required', 'string', 'max:20'],
            'course_title'  => ['required', 'string', 'max:120'],
            'credit_unit'   => ['required', 'integer', 'min:1', 'max:6'],
        ]);

        $validated['course_code'] = strtoupper(trim($validated['course_code']));

        $exists = CgpaCourseOption::where('level', $validated['level'])
            ->where('semester_type', $validated['semester_type'])
            ->where('course_code', $validated['course_code'])
            ->exists();

        if ($exists) {
            return back()->withErrors([
                'course_code' => "{$validated['course_code']} already exists for this level/semester.",
            ])->withInput();
        }

        CgpaCourseOption::create($validated);

        return back()->with('success', "{$validated['course_code']} added to the course list.");
    }

    public function update(Request $request, CgpaCourseOption $option): RedirectResponse
    {
        $validated = $request->validate([
            'course_title' => ['required', 'string', 'max:120'],
            'credit_unit'  => ['required', 'integer', 'min:1', 'max:6'],
            'is_active'    => ['nullable', 'boolean'],
        ]);

        $option->update([
            'course_title' => $validated['course_title'],
            'credit_unit'  => $validated['credit_unit'],
            'is_active'    => $request->boolean('is_active'),
        ]);

        return back()->with('success', "{$option->course_code} updated.");
    }

    public function destroy(CgpaCourseOption $option): RedirectResponse
    {
        $code = $option->course_code;
        $option->delete();

        return back()->with('success', "{$code} removed from the course list.");
    }
}