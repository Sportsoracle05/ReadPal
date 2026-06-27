<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class QuestionGenerationController extends Controller
{

public function generate(Request $request, $id)
{
    $material = \App\Models\Material::find($id);

    if (!$material) {
        return response()->json([
            'error' => "Material with ID {$id} not found."
        ], 404);
    }

    $text = $material->note_text ?? '';

    \Log::info("Generating questions for Material ID {$id}, note_text length: " . strlen($text));

    if (trim($text) === '') {
        return response()->json([
            'error' => "No extracted text found for material ID {$id}. note_text is empty."
        ], 400);
    }

    try {
        // 1️⃣ Call OpenAI API
        \Log::info("Calling OpenAI API for Material ID {$id}...");
        $apiResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
            'Content-Type' => 'application/json',
        ])->timeout(60)->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4o-mini',
            'messages' => [
    [
        'role' => 'system',
        'content' => "You are a quiz generator. Generate 50 multiple-choice questions (A–D) from the given material text.
        
        CRITICAL RULES:
        1. Randomize the position of the correct answer across options A, B, C, and D. Do not default to 'A'.
        2. Return ONLY valid JSON in a flat array.
        3. Structure:
        [
          {
            \"question\": \"...\",
            \"options\": {\"A\": \"...\", \"B\": \"...\", \"C\": \"...\", \"D\": \"...\"},
            \"correct_answer\": \"[A, B, C, or D]\"
          }
        ]"
    ],
    ['role' => 'user', 'content' => \Illuminate\Support\Str::limit($text, 4000)],
],

        ]);

        if ($apiResponse->failed()) {
            \Log::error("OpenAI API call failed for Material ID {$id}: " . $apiResponse->body());
            return response()->json([
                'error' => 'OpenAI API call failed',
                'status' => $apiResponse->status(),
                'body' => $apiResponse->body()
            ], 500);
        }

        $content = $apiResponse->json()['choices'][0]['message']['content'] ?? null;

        if (!$content) {
            \Log::error("OpenAI API returned empty content for Material ID {$id}: " . $apiResponse->body());
            return response()->json([
                'error' => 'No questions returned from API',
                'raw_response' => $apiResponse->body()
            ], 500);
        }

        // 2️⃣ Clean response
        $content = trim($content);
        $content = preg_replace('/^```json/i', '', $content);
        $content = preg_replace('/^```/', '', $content);
        $content = preg_replace('/```$/', '', $content);
        $content = trim($content);

        // 3️⃣ Decode JSON
        $json = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            if (preg_match('/(\[.*\])/s', $content, $matches)) {
                $json = json_decode($matches[1], true);
            }
        }

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($json)) {
            \Log::error("Invalid JSON returned for Material ID {$id}: " . substr($content, 0, 500));
            return response()->json([
                'error' => 'Invalid JSON format returned',
                'raw_content' => substr($content, 0, 500),
                'json_error' => json_last_error_msg()
            ], 500);
        }

        // 4️⃣ Save JSON file
        $filename = "questions/material_{$material->id}.json";
        \Illuminate\Support\Facades\Storage::disk('public')->put($filename, json_encode($json, JSON_PRETTY_PRINT));

        \Log::info("Questions generated successfully for Material ID {$id}, saved to {$filename}");

        return response()->json([
            'message' => 'Questions generated successfully!',
            'file' => asset("storage/{$filename}")
        ]);

    } catch (\Exception $e) {
        \Log::error("Exception while generating questions for Material ID {$id}: " . $e->getMessage());
        return response()->json([
            'error' => 'Exception occurred while generating questions',
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
}





    public function takeQuiz($id)
    {
        $material = \App\Models\Material::findOrFail($id);
        $resource = $material->resource;
        $path = public_path("storage/questions/material_{$id}.json");

        if (!file_exists($path)) {
            abort(404, 'No quiz found for this material.');
        }

        $questions = collect(json_decode(file_get_contents($path), true))
            ->shuffle()
            ->take(15)
            ->values()
            ->toArray();

        return view('quiz.take', compact('material', 'resource', 'questions'));
    }
    
    public function submitQuiz(Request $request, $id)
    {
        $path = public_path("storage/questions/material_{$id}.json");

        if (!file_exists($path)) {
            abort(404, 'Quiz file not found.');
        }

        $questions = json_decode(file_get_contents($path), true);

        $score = 0;
        foreach ($questions as $index => $q) {
            $userAnswer = $request->input("question_$index");
            if ($userAnswer && strtoupper($userAnswer) === strtoupper($q['correct_answer'])) {
                $score++;
            }
        }

        return view('quiz.result', [
            'score' => $score,
            'total' => count($questions),
            'material' => \App\Models\Material::find($id)
        ]);
    }


}