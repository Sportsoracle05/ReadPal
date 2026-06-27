@extends('layouts.app')
@section('title', 'Reading Material')

@section('content')
<style>
  /* Typography & Prose Styles */
  .prose ul { list-style-type: disc; margin-left: 1.5rem; }
  .prose ol { list-style-type: decimal; margin-left: 1.5rem; }
  .prose h1 { font-size: 2.25rem; font-weight: 700; margin-bottom: 1rem; margin-top: 1.5rem; line-height: 2.5rem; }
  .prose h2 { font-size: 1.875rem; font-weight: 700; margin-bottom: 0.75rem; margin-top: 1.25rem; line-height: 2.25rem; }
  .prose h3 { font-size: 1.5rem; font-weight: 600; margin-bottom: 0.5rem; margin-top: 1rem; }
  .prose h4 { font-size: 1.25rem; font-weight: 600; }

  /* UI Components */
  .btn-primary { background-color: #2563eb; color: white; padding: 0.5rem 1rem; border-radius: 0.375rem; transition: all 0.2s; display: inline-block; }
  .btn-primary:hover { background-color: #1d4ed8; }
  
  .card { border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); transition: all 0.2s; }
  .card:hover { box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }

  /* A4 Paper Simulation */
  .paper-container {
    background-color: white;
    shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    padding: 2.5rem;
    border-radius: 0.5rem;
    border: 1px solid #e2e8f0;
    width: 100%;
    max-width: 794px;
    margin: 0 auto;
    min-height: 1123px; /* A4 Ratio approximation */
    box-sizing: border-box;
  }
</style>

<main class="flex-grow container mx-auto px-4 py-8">
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('resources.show', $resource) }}" class="text-blue-600 hover:underline">&larr; Back to Materials</a>
        <h1 class="text-3xl font-bold">Reading Material</h1>
    </div>

    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
                <h3 class="text-sm font-medium text-gray-500">Title</h3>
                <p class="mt-1 text-lg font-semibold">{{ $material->title }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Course</h3>
                <p class="mt-1 text-lg font-semibold">{{ $resource->course_code }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Pages</h3>
                <p class="mt-1 text-lg font-semibold">{{ $chunks->lastPage() }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Uploaded</h3>
                <p class="mt-1 text-lg font-semibold">{{ $material->created_at->format('d/m/Y') }}</p>
            </div>
        </div>
    </div>

    @if ($material->type == 'pdf')
        <div class="bg-white rounded-lg shadow p-8 text-center mb-8">
            <p class="mb-4 text-gray-600">This material is available as a PDF document.</p>
            <a href="{{ asset($material->pdf_path) }}" class="btn-primary">
                Download PDF Material
            </a>
        </div>
    @else
         <div class="bg-white shadow-lg p-6 sm:p-10 rounded-lg border border-gray-300 w-full max-w-[794px]"
          style="aspect-ratio: 794 / 1123; font-size: clamp(14px, 2vw, 16px); line-height: 1.7; word-wrap: break-word; box-sizing: border-box;">

          <h2 class="text-center text-lg sm:text-2xl font-bold mb-2">{{ $material->title }}</h2>

          <div class="text-sm sm:text-base mb-3">
              <p><strong>Course:</strong> {{ $resource->course_code }}</p>
              <p><strong>Lecturer:</strong> {{ $resource->lecturer }}</p>
          </div>

          <hr class="mb-4">

          <div class="prose prose-sm sm:prose-base max-w-none text-justify font-serif">
    {!! $chunks->first() !!}
</div>
      </div>
  </div>

        <div class="mb-8">
            <x-material-pagination :pagination="$chunks" />
        </div>

        <div class="bg-white rounded-lg shadow p-6 text-center">
            <a href="{{ route('material.download', ['resource' => $resource->slug, 'material' => Str::slug($material->title)]) }}" 
               class="btn-primary">
                Download Page as PDF
            </a>
        </div>
    @endif
</main>
@endsection
