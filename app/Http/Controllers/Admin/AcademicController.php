<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AcademicSession;
use App\Models\AcademicSemester;
use App\Models\ActiveSemesterSetting;

class AcademicController extends Controller
{
    public function index()
    {
        $sessions = AcademicSession::with('semesters')->get();
        
        // Flatten all semesters from all sessions into one collection
        $allSemesters = $sessions->flatMap->semesters;

        // Ensure we use the correct variable name for the active one
        $activeSemester = ActiveSemesterSetting::first()?->semester;

        return view('admin.academic.index', compact('sessions', 'allSemesters', 'activeSemester'));
    }


    public function storeSession(Request $request)
    {
        AcademicSession::create([
            'name' => $request->name
        ]);

        return back()->with('success', "Academic session '{$request->name}' created successfully.");
    }

    public function storeSemester(Request $request)
    {
        AcademicSemester::create([
            'session_id' => $request->session_id,
            'name' => $request->name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

         return back()->with('success', "New semester added to the session.");
    }

    public function selectSemester($id)
    {
        ActiveSemesterSetting::query()->delete();

        ActiveSemesterSetting::create([
            'semester_id' => $id
        ]);

        return back()->with('success', "Now set as the active semester.");
    }
}