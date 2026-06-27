<?php

namespace App\Services;

use App\Models\Semester;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * CgpaCalculatorService
 *
 * Handles all GPA / CGPA computation for the AAUA 5.0 scale.
 *
 * AAUA Grading:
 *   A = 5 pts (70-100)
 *   B = 4 pts (60-69)
 *   C = 3 pts (50-59)
 *   D = 2 pts (45-49)
 *   E = 1 pt  (40-44)
 *   F = 0 pts (0-39)
 */
class CgpaCalculatorService
{
    // ---------------------------------------------------------------
    //  Semester-Level GPA
    // ---------------------------------------------------------------

    /**
     * Calculate GPA for a single semester.
     *
     * Formula: Σ(unit × grade_point) / Σ(unit)
     *
     * @param  Semester  $semester  Must have courses loaded (eager or lazy)
     * @return float  GPA rounded to 2 decimal places (0.00 – 5.00)
     */
    public function semesterGpa(Semester $semester): float
    {
        // Ensure courses are loaded
        if (! $semester->relationLoaded('courses')) {
            $semester->load('courses');
        }

        $totalUnits         = 0;
        $totalQualityPoints = 0;

        foreach ($semester->courses as $course) {
            $totalUnits         += $course->unit;
            $totalQualityPoints += $course->quality_point; // unit × grade_point
        }

        if ($totalUnits === 0) {
            return 0.00;
        }

        return round($totalQualityPoints / $totalUnits, 2);
    }

    // ---------------------------------------------------------------
    //  Cumulative CGPA (across all semesters)
    // ---------------------------------------------------------------

    /**
     * Calculate the overall CGPA for a user across all their semesters.
     *
     * Formula: Σ all quality points / Σ all units (all semesters)
     *
     * @param  User  $user
     * @return float  CGPA rounded to 2 decimal places
     */
    public function overallCgpa(User $user): float
    {
        $semesters = $this->userSemestersWithCourses($user);

        $totalUnits         = 0;
        $totalQualityPoints = 0;

        foreach ($semesters as $semester) {
            foreach ($semester->courses as $course) {
                $totalUnits         += $course->unit;
                $totalQualityPoints += $course->quality_point;
            }
        }

        if ($totalUnits === 0) {
            return 0.00;
        }

        return round($totalQualityPoints / $totalUnits, 2);
    }

    // ---------------------------------------------------------------
    //  Aggregate Stats
    // ---------------------------------------------------------------

    /**
     * Return a summary array for the dashboard:
     * [cgpa, total_units, total_quality_points, semester_count, grade_class]
     *
     * @param  User  $user
     * @return array<string, mixed>
     */
    public function dashboardStats(User $user): array
    {
        $semesters = $this->userSemestersWithCourses($user);

        $totalUnits         = 0;
        $totalQualityPoints = 0;

        foreach ($semesters as $semester) {
            foreach ($semester->courses as $course) {
                $totalUnits         += $course->unit;
                $totalQualityPoints += $course->quality_point;
            }
        }

        $cgpa = $totalUnits > 0
            ? round($totalQualityPoints / $totalUnits, 2)
            : 0.00;

        return [
            'cgpa'                 => $cgpa,
            'total_units'          => $totalUnits,
            'total_quality_points' => $totalQualityPoints,
            'semester_count'       => $semesters->count(),
            'grade_class'          => $this->gradeClass($cgpa),
        ];
    }

    /**
     * Build per-semester breakdown for the index page.
     *
     * @param  User  $user
     * @return Collection<int, array<string, mixed>>
     */
    public function semesterBreakdown(User $user): Collection
    {
        return $this->userSemestersWithCourses($user)
            ->map(function (Semester $semester) {
                return [
                    'semester'      => $semester,
                    'gpa'           => $this->semesterGpa($semester),
                    'total_units'   => $semester->total_units,
                    'quality_pts'   => $semester->total_quality_points,
                    'course_count'  => $semester->courses->count(),
                ];
            });
    }

    // ---------------------------------------------------------------
    //  AAUA Classification
    // ---------------------------------------------------------------

    /**
     * Map a CGPA value to an AAUA degree class label.
     */
    public function gradeClass(float $cgpa): string
    {
        return match (true) {
            $cgpa >= 4.50 => 'First Class',
            $cgpa >= 3.50 => 'Second Class Upper',
            $cgpa >= 2.40 => 'Second Class Lower',
            $cgpa >= 1.50 => 'Third Class',
            $cgpa >= 1.00 => 'Pass',
            default       => 'Fail',
        };
    }

    // ---------------------------------------------------------------
    //  Private Helpers
    // ---------------------------------------------------------------

    private function userSemestersWithCourses(User $user): Collection
    {
        return Semester::with('courses')
            ->where('user_id', $user->id)
            ->orderBy('level')
            ->orderBy('semester_type')
            ->get();
    }
}
