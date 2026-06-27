<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use Illuminate\View\View;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Barryvdh\DomPDF\Facade\Pdf;

class ResourcesController extends Controller
{
    public function index()
    {
        $layout = auth()->check() ? 'layouts.app' : 'layouts.guest';
        $resources = Resource::latest()->get();

        return view('resources.index', compact('layout', 'resources'));
    }

    public function show(Resource $resource)
{
    $materials = $resource->materials()
        ->latest()
        ->paginate(10);

    return view('resources.show', compact('resource', 'materials'));
}

public function full(Resource $resource): View
    {
        $materials = $resource->materials()
            ->orderBy('id', 'asc')
            ->get();

        return view('resources.full.show', [
            'resource'  => $resource,
            'materials' => $materials,
        ]);
    }

public function view(Material $material)
{
    $path = storage_path('app/public/' . $material->pdf_path);

    if (!file_exists($path)) {
        abort(404);
    }

    return response()->file($path);
}

public function material(Resource $resource, Material $material)
{
    if ((int) $material->resource_id !== (int) $resource->id) {
        abort(404);
    }

    $charsPerPage = 5000;
    $content = $material->note_text ?? '';

    // Split into word chunks
    $words = preg_split('/\s+/', trim($content));
    $chunks = [];
    $currentChunk = '';

    foreach ($words as $word) {
        if (mb_strlen($currentChunk . ' ' . $word) > $charsPerPage) {
            $chunks[] = trim($currentChunk);
            $currentChunk = $word;
        } else {
            $currentChunk .= ' ' . $word;
        }
    }

    if ($currentChunk !== '') {
        $chunks[] = trim($currentChunk);
    }

    // ✅ Use query string directly to avoid implicit binding interference
    $currentPage = (int) request()->query('page', 1);
    $currentPage = max(1, min($currentPage, count($chunks))); // clamp to valid range

    $paginator = new LengthAwarePaginator(
        array_slice($chunks, $currentPage - 1, 1),
        count($chunks),
        1,
        $currentPage,
        [
            'path'  => request()->url(),
            'query' => request()->query(),
        ]
    );

    return view('resources.lessons.material', [
        'resource' => $resource,
        'material' => $material,
        'chunks'   => $paginator,
    ]);
}

    


    // ✅ Generate PDF using DOMPDF
public function downloadPdf(Resource $resource, Material $material)
{
    // Get raw text (do NOT escape with e())
    $text = $material->note_text ?? '';

    // Convert newlines to paragraphs
    $text = nl2br($text);                     // Turn \n into <br>
    $text = str_replace('<br />', "</p><p>", $text); // Make paragraphs
    $text = "<p>$text</p>";                   // Wrap everything in <p>

    // Prepare view data
    $data = [
        'title'       => $material->title,
        'course_code' => $resource->course_code,
        'lecturer'    => $resource->lecturer,
        'content'     => $text, // Raw HTML
    ];

    // Create PDF
    $pdf = Pdf::loadView('resources.pdf.material_pdf', $data)
        ->setPaper('A4', 'portrait')
        ->setOption('isHtml5ParserEnabled', true)
        ->setOption('isRemoteEnabled', true);

    // File name
    $fileName = str_replace(' ', '_', $material->title) . '.pdf';

    return $pdf->download($fileName);
}

public function downloadAllMaterialsPdf(Resource $resource)
{
    // Load materials ordered properly
    $materials = $resource->materials()->orderBy('id')->get();

    $compiledContent = '';
    $lessonNumber = 1;

    foreach ($materials as $material) {

        $text = $material->note_text ?? '';

        // Convert newlines to paragraphs
        $text = nl2br($text);
        $text = str_replace('<br />', "</p><p>", $text);
        $text = "<p>$text</p>";

        $compiledContent .= "
            <div style='page-break-after: always;'>
                <h2>Lesson {$lessonNumber}: {$material->title}</h2>
                {$text}
            </div>
        ";

        $lessonNumber++;
    }

    $data = [
        'title'       => $resource->course_title,
        'course_code' => $resource->course_code,
        'lecturer'    => $resource->lecturer,
        'content'     => $compiledContent,
    ];

    $pdf = Pdf::loadView('resources.pdf.material_pdf', $data)
        ->setPaper('A4', 'portrait')
        ->setOption('isHtml5ParserEnabled', true)
        ->setOption('isRemoteEnabled', true);

    $fileName = str_replace(' ', '_', $resource->course_title) . '_Full_Notes.pdf';

    return $pdf->download($fileName);
}

}
