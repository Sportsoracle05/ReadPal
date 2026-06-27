@extends('layouts.app')
@section('title', 'Extract Text')
@section('content')
<div class="max-w-3xl mx-auto bg-white shadow-md rounded-lg p-6">
    <h2 class="text-2xl font-bold mb-2">{{ $material->title }}</h2>
    <p class="text-gray-600 mb-4">{{ $material->description }}</p>

    <a href="{{ asset('storage/' . $material->pdf_path) }}" 
       target="_blank" 
       class="inline-block bg-blue-600 text-white px-4 py-2 rounded mb-4">
        View Material
    </a>

    <hr class="my-4">

    {{-- Extract Text --}}
    <form id="extractForm" class="mb-4">
        @csrf
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">
            Extract Text
        </button>
    </form>

    {{-- Generate Questions --}}
    <form id="generateForm" class="mb-4 hidden">
        @csrf
        <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded">
            Generate Questions
        </button>
    </form>

    {{-- Loading Spinner --}}
    <div id="loading" class="hidden text-center my-4 text-gray-500">Processing...</div>

    {{-- Results --}}
    <div id="results" class="mt-6 hidden bg-gray-50 p-4 rounded border border-gray-200">
        <h3 class="font-semibold mb-2 text-lg">Extracted Preview:</h3>
        <pre id="textPreview" class="text-sm bg-white p-2 rounded border"></pre>

        <a id="jsonLink" href="#" target="_blank"
           class="mt-4 inline-block bg-blue-600 text-white px-4 py-2 rounded hidden">
            Download Questions JSON
        </a>
    </div>
</div>

{{-- Simple JS to handle AJAX calls --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const extractForm = document.getElementById('extractForm');
    const generateForm = document.getElementById('generateForm');
    const results = document.getElementById('results');
    const textPreview = document.getElementById('textPreview');
    const jsonLink = document.getElementById('jsonLink');
    const loading = document.getElementById('loading');

    extractForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        loading.classList.remove('hidden');

        const response = await fetch("{{ route('materials.extract', $material->id) }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        });

        const data = await response.json();
        loading.classList.add('hidden');

        if (data.text_preview) {
            results.classList.remove('hidden');
            textPreview.textContent = data.text_preview;
            generateForm.classList.remove('hidden');
        } else {
            alert(data.error || 'Extraction failed.');
        }
    });

    generateForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        loading.classList.remove('hidden');

        const response = await fetch("{{ route('materials.generate', $material->id) }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        });

        const data = await response.json();
        loading.cl
