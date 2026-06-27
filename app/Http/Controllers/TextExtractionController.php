<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;
use Smalot\PdfParser\Parser;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Element\Text;
use PhpOffice\PhpWord\Element\Cell;

class TextExtractionController extends Controller
{
    public function extract(Request $request, $id)
    {
        $material = Material::findOrFail($id);

        $filePath = trim(public_path('storage/' . $material->file_path));

        if (!file_exists($filePath)) {
            return response()->json([
                'error' => 'File not found.',
                'checked_path' => $filePath
            ], 404);
        }

        $extension = strtolower(trim(pathinfo($filePath, PATHINFO_EXTENSION)));
        $text = '';

        if ($extension === 'pdf') {
            $parser = new Parser();
            $pdf = $parser->parseFile($filePath);
            $text = $pdf->getText();
        } 
        elseif ($extension === 'txt') {
            $text = file_get_contents($filePath);
        } 
        elseif (in_array($extension, ['doc', 'docx'])) {
            $phpWord = IOFactory::load($filePath);

            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    $text .= $this->extractElementText($element);
                }
            }
        } 
        else {
            return response()->json([
                'error' => 'Unsupported file type.',
                'extension_detected' => $extension
            ], 400);
        }

        $material->update(['note_text' => $text]);

        return redirect()
            ->route('admin.materials.index')
            ->with('success', 'Text extracted successfully.');
    }

    private function extractElementText($element): string
    {
        $text = '';

        // 1️⃣ TextRun (container of multiple text elements)
        if ($element instanceof TextRun) {
            foreach ($element->getElements() as $child) {
                $text .= $this->extractElementText($child);
            }
            $text .= "\n";
        } 
        // 2️⃣ Table
        elseif ($element instanceof Table) {
            foreach ($element->getRows() as $row) {
                foreach ($row->getCells() as $cell) {
                    foreach ($cell->getElements() as $cellElement) {
                        $text .= $this->extractElementText($cellElement) . "\t";
                    }
                    $text .= "\n";
                }
            }
        } 
        // 3️⃣ Cell (just extract its children)
        elseif ($element instanceof Cell) {
            foreach ($element->getElements() as $cellElement) {
                $text .= $this->extractElementText($cellElement);
            }
        } 
        // 4️⃣ Simple Text
        elseif ($element instanceof Text) {
            $text .= $element->getText() . "\n";
        }

        // Ignore any other unknown element types (like Images, PageBreaks, etc.)
        return $text;
    }
}