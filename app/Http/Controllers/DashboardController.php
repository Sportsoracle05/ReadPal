<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Resource;
use App\Models\Material;
use App\Models\Lecture;
use App\Models\UserQuizAttempt;

class DashboardController extends Controller
{

public function index() 
{
    $user = auth()->user();

    // Fetch data
    $resources = Resource::latest()->get();
    $materials = Material::latest()->take(3)->get();

    // Remove expired lectures
    Lecture::whereRaw('DATE_ADD(start_time, INTERVAL duration_minutes MINUTE) < NOW()')->delete();

    // Fetch upcoming/ongoing lectures
    $lectures = Lecture::with('resource')
        ->orderBy('start_time', 'asc')
        ->whereRaw('DATE_ADD(start_time, INTERVAL duration_minutes MINUTE) > NOW()')
        ->get();

    // Count totals
    $totalResources = Resource::count();
    $totalMaterials = Material::count();
    $totalLectures = Lecture::count();
    $totalCompletedQuizzes = UserQuizAttempt::where('user_id', $user->id)->count();

    // Prepare the data array so it exists for the JSON response
    $data = compact('user', 'resources', 'materials', 'totalResources', 'totalMaterials', 'totalLectures', 'lectures', 'totalCompletedQuizzes');


    return view('dashboard', $data);
}

}
