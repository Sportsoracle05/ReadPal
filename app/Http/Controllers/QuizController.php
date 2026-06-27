<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Resource;
use App\Models\UserQuizAttempt;
use App\Models\Material;



class QuizController extends Controller
{
    /**
     * Show all materials that have a corresponding quiz JSON file.
     */
  public function index(Request $request)
    {
        $user = Auth::user();
        $filterCourse = $request->query('course_code');
        $perPage = 10;

        // Start query with the relationship
        $query = \App\Models\Material::with('resource');

        // Apply course_code filter if provided
        if ($filterCourse) {
            $query->whereHas('resource', function ($q) use ($filterCourse) {
                $q->where('course_code', $filterCourse);
            });
        }

        // Get all materials first to check for JSON existence
        $allMaterials = $query->get()->filter(function ($res) {
            $jsonPath = public_path("storage/questions/material_{$res->id}.json");
            return file_exists($jsonPath);
        });

        // Paginate manually
        $page = $request->get('page', 1);
        $total = $allMaterials->count();
        $resources = $allMaterials->forPage($page, $perPage);
        $resources = new \Illuminate\Pagination\LengthAwarePaginator(
            $resources,
            $total,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Get distinct course codes for dropdown
        $courseCodes = \App\Models\Material::with('resource')
            ->get()
            ->pluck('resource.course_code')
            ->unique()
            ->sort()
            ->values();

        // Get user attempts
        $attempts = \App\Models\UserQuizAttempt::where('user_id', $user->id)
            ->get()
            ->keyBy('material_id');

        return view('quiz.index', compact('resources', 'attempts', 'courseCodes', 'filterCourse'));
    }





    /**
     * Store or update a user's quiz attempt for a material.
     */
    public function storeQuizResult(Request $request, $materialId)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in to save your quiz result.');
        }

        // ✅ Validate input safely
        $data = $request->validate([
            'score' => 'required|integer|min:0',
            'attempt' => 'required|integer|min:1',
        ]);

        // ✅ Find existing attempt for user/material
        $attempt = UserQuizAttempt::where('user_id', $user->id)
            ->where('material_id', $materialId)
            ->first();

        if ($attempt) {
            // Increment attempt count
            $attempt->attempt += 1;

            // Update highest score if current score is higher
            if ($data['score'] > $attempt->score) {
                $attempt->score = $data['score'];
            }

            $attempt->save();
        } else {
            // ✅ Create a new record for first attempt
            UserQuizAttempt::create([
                'user_id' => $user->id,
                'material_id' => $materialId,
                'total' => 30, 
                'score' => $data['score'],
                'attempt' => 1,
            ]);
        }

        // ✅ Redirect to dashboard (or back to test list)
        return redirect()->route('quiz.index')->with('success', 'Your quiz result has been recorded successfully!');
    }
}
